<?php

namespace Modules\AI\Services;

use Modules\AI\Support\CompanyContentProfile;
use Modules\AI\Support\QuoteGenerationSchema;
use Modules\Base\Support\CompanyBranding;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\QuoteLine;
use Modules\Product\Models\Product;
use Modules\Services\Enums\ServiceStatus;
use Modules\Services\Models\Service;
use Modules\Tax\Models\TaxRate;

class QuoteGenerationService
{
    public function __construct(private readonly ContentGenerationService $contentGenerationService) {}

    /**
     * @return array{success: bool, fields: ?array<string, mixed>, error: ?string, provider: ?string}
     */
    public function generate(int $companyId, int $dealId, ?string $prompt = null, ?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $company = Company::query()->find($companyId);
        $deal = Deal::query()->with(['services', 'pipelineStage'])->find($dealId);

        if ($company === null || $deal === null || (int) $deal->company_id !== (int) $company->id) {
            return [
                'success' => false,
                'fields' => null,
                'error' => __('crm::quote.validation.deal_exists'),
                'provider' => null,
            ];
        }

        $catalog = $this->catalog($locale, $deal);
        $fallbackLines = $this->dealFallbackLines($deal, $locale);

        if ($catalog['services'] === [] && $catalog['products'] === [] && $fallbackLines === []) {
            return [
                'success' => false,
                'fields' => null,
                'error' => __('crm::quote.ai.no_catalog'),
                'provider' => null,
            ];
        }

        $context = $this->context($company, $deal, $catalog, $fallbackLines, $locale);
        $generated = $this->contentGenerationService->generateJson(
            QuoteGenerationSchema::systemPrompt($locale, $context),
            QuoteGenerationSchema::userMessage($context, $prompt),
        );

        if (! $generated['success']) {
            return [
                'success' => false,
                'fields' => null,
                'error' => $generated['error'] ?? __('ai::content_generation.messages.request_failed'),
                'provider' => $generated['provider'],
            ];
        }

        $fields = QuoteGenerationSchema::normalize(
            $generated['data'] ?? [],
            $catalog,
            $fallbackLines,
            (string) ($deal->currency ?: 'USD'),
            $this->defaultTaxPercent(),
        );

        if ($fields['lines'] === []) {
            return [
                'success' => false,
                'fields' => null,
                'error' => __('crm::quote.ai.no_lines'),
                'provider' => $generated['provider'],
            ];
        }

        $issuedAt = now()->toDateString();

        return [
            'success' => true,
            'fields' => [
                'terms' => $fields['terms'],
                'notes' => $fields['notes'],
                'currency' => $fields['currency'],
                'issued_at' => $issuedAt,
                'expires_at' => now()->addDays($fields['validity_days'])->toDateString(),
                'lines' => $fields['lines'],
            ],
            'error' => null,
            'provider' => $generated['provider'],
        ];
    }

    /**
     * @param  array{services: array<int, array<string, mixed>>, products: array<int, array<string, mixed>>}  $catalog
     * @param  list<array<string, mixed>>  $fallbackLines
     * @return array<string, mixed>
     */
    private function context(Company $company, Deal $deal, array $catalog, array $fallbackLines, string $locale): array
    {
        $branding = CompanyBranding::forInvoice();
        $facts = CompanyContentProfile::facts();

        return [
            'issuer_name' => (string) ($facts['Company name'] ?: ($branding['name'] ?? config('app.name'))),
            'customer_name' => $company->name,
            'provider' => array_filter([
                'name' => $facts['Company name'] ?: ($branding['name'] ?? null),
                'about' => $facts['About'] ?? null,
                'phone' => $branding['phone'] ?? ($facts['Phone'] ?? null),
                'email' => $branding['email'] ?? ($facts['Email'] ?? null),
                'address' => $branding['address'] ?? ($facts['Address'] ?? null),
            ]),
            'customer' => array_filter([
                'id' => $company->id,
                'name' => $company->name,
                'activity_type' => $company->activity_type,
                'email' => $company->email,
                'phone' => $company->phone,
                'country' => $company->country,
                'city' => $company->city,
                'address' => $company->address,
                'notes' => $this->limit((string) $company->notes),
            ], static fn (mixed $value): bool => $value !== null && $value !== ''),
            'deal' => array_filter([
                'id' => $deal->id,
                'title' => $deal->title,
                'value' => $deal->value !== null ? (float) $deal->value : null,
                'currency' => $deal->currency,
                'probability' => $deal->probability,
                'expected_close_date' => $deal->expected_close_date?->toDateString(),
                'stage' => $deal->pipelineStage?->name,
                'description' => $this->limit((string) $deal->description),
                'attached_services' => $fallbackLines,
            ], static fn (mixed $value): bool => $value !== null && $value !== '' && $value !== []),
            'catalog' => [
                'services' => array_values($catalog['services']),
                'products' => array_values($catalog['products']),
            ],
            'locale' => $locale,
        ];
    }

    /**
     * @return array{services: array<int, array<string, mixed>>, products: array<int, array<string, mixed>>}
     */
    private function catalog(string $locale, Deal $deal): array
    {
        $services = [];
        foreach (Service::query()->orderBy('id')->limit(40)->get() as $service) {
            if ($service->status === ServiceStatus::ARCHIVED->value) {
                continue;
            }

            $services[$service->id] = $this->serviceCatalogRow($service, $locale);
        }

        foreach ($deal->services as $service) {
            $services[$service->id] = $this->serviceCatalogRow($service, $locale);
        }

        $products = [];
        foreach (Product::query()->with('taxRate')->active()->orderBy('id')->limit(40)->get() as $product) {
            $products[$product->id] = array_filter([
                'id' => $product->id,
                'name' => $product->getTranslation('name', $locale) ?: $product->getTranslation('name', 'en'),
                'price' => (float) $product->price,
                'currency' => $product->currency,
                'tax_percent' => (float) ($product->taxRate?->percentage ?? 0),
                'description' => $this->limit((string) $product->getTranslation('short_description', $locale)),
            ], static fn (mixed $value): bool => $value !== null && $value !== '');
        }

        return [
            'services' => $services,
            'products' => $products,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function dealFallbackLines(Deal $deal, string $locale): array
    {
        $lines = [];

        foreach ($deal->services as $service) {
            $lines[] = [
                'item_type' => QuoteLine::TYPE_SERVICE,
                'service_id' => $service->id,
                'product_id' => null,
                'description' => $service->getTranslation('title', $locale) ?: $service->getTranslation('title', 'en'),
                'quantity' => max(1, (int) $service->pivot->quantity),
                'unit_price' => (float) $service->pivot->unit_price,
                'discount_percent' => 0,
                'tax_percent' => $this->defaultTaxPercent(),
            ];
        }

        return $lines;
    }

    /**
     * @return array<string, mixed>
     */
    private function serviceCatalogRow(Service $service, string $locale): array
    {
        return array_filter([
            'id' => $service->id,
            'title' => $service->getTranslation('title', $locale) ?: $service->getTranslation('title', 'en'),
            'description' => $this->limit((string) $service->getTranslation('description', $locale)),
        ], static fn (mixed $value): bool => $value !== null && $value !== '');
    }

    private function defaultTaxPercent(): float
    {
        try {
            return round((float) (TaxRate::resolveDefault()?->percentage ?? 0), 2);
        } catch (\Throwable) {
            return 0.0;
        }
    }

    private function limit(string $value): string
    {
        $value = trim(preg_replace('/\s+/', ' ', strip_tags($value)) ?? '');

        return mb_strlen($value) > 400 ? mb_substr($value, 0, 397).'...' : $value;
    }
}

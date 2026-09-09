<?php

namespace Modules\CRM\Services\Quote;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Base\Support\CompanyBranding;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\PipelineStage;
use Modules\CRM\Models\Quote;
use Modules\CRM\Models\QuoteLine;
use Modules\CRM\Services\Deal\DealService;
use Modules\Finance\Services\CurrencyService;
use Modules\Product\Models\Product;
use Modules\Project\Models\Project;
use Modules\Services\Models\Service;
use Symfony\Component\HttpFoundation\Response;

class QuoteService
{
    public function __construct(
        private readonly CurrencyService $currencyService,
        private readonly DealService $dealService,
    ) {}

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return Quote::query()
            ->with(['company:id,name', 'deal:id,title'])
            ->filter($filters)
            ->latest()
            ->paginate((int) config('core.page_size', 15));
    }

    public function create(array $data): Quote
    {
        return DB::transaction(function () use ($data) {
            $lines = $this->normalizeLines($data['lines'] ?? []);
            $totals = $this->sumTotals($lines);

            $quote = Quote::query()->create([
                'quote_number' => $this->generateQuoteNumber(),
                'company_id' => $data['company_id'],
                'deal_id' => $data['deal_id'],
                'status' => Quote::STATUS_DRAFT,
                'subtotal' => $totals['subtotal'],
                'discount_amount' => $totals['discount_amount'],
                'tax_amount' => $totals['tax_amount'],
                'total' => $totals['total'],
                'currency' => strtoupper($data['currency'] ?? $this->currencyService->defaultCurrency()),
                'terms' => $data['terms'] ?? null,
                'notes' => $data['notes'] ?? null,
                'issued_at' => $data['issued_at'] ?? now()->toDateString(),
                'expires_at' => $data['expires_at'] ?? null,
            ]);

            $this->syncLines($quote, $lines);

            return $quote->load(['lines', 'company', 'deal']);
        });
    }

    public function update(Quote $quote, array $data): Quote
    {
        if (! in_array($quote->status, [Quote::STATUS_DRAFT, Quote::STATUS_SENT], true)) {
            throw ValidationException::withMessages([
                'status' => __('crm::quote.messages.not_editable'),
            ]);
        }

        return DB::transaction(function () use ($quote, $data) {
            $lines = $this->normalizeLines($data['lines'] ?? []);
            $totals = $this->sumTotals($lines);

            $quote->update([
                'company_id' => $data['company_id'],
                'deal_id' => $data['deal_id'],
                'subtotal' => $totals['subtotal'],
                'discount_amount' => $totals['discount_amount'],
                'tax_amount' => $totals['tax_amount'],
                'total' => $totals['total'],
                'currency' => strtoupper($data['currency'] ?? $quote->currency),
                'terms' => $data['terms'] ?? null,
                'notes' => $data['notes'] ?? null,
                'issued_at' => $data['issued_at'] ?? $quote->issued_at,
                'expires_at' => $data['expires_at'] ?? null,
            ]);

            $this->syncLines($quote, $lines);

            return $quote->fresh(['lines', 'company', 'deal']);
        });
    }

    public function createFromDeal(Deal $deal): Quote
    {
        $deal->loadMissing('services');

        $lines = [];
        $sortOrder = 0;

        foreach ($deal->services as $service) {
            $quantity = max(1, (int) $service->pivot->quantity);
            $unitPrice = (float) $service->pivot->unit_price;
            $amounts = QuoteLine::calculateAmounts($quantity, $unitPrice);

            $lines[] = [
                'item_type' => QuoteLine::TYPE_SERVICE,
                'service_id' => $service->id,
                'product_id' => null,
                'description' => $service->getTranslation('title', app()->getLocale()),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount_percent' => 0,
                'tax_percent' => 0,
                'discount_amount' => $amounts['discount_amount'],
                'tax_amount' => $amounts['tax_amount'],
                'amount' => $amounts['amount'],
                'sort_order' => $sortOrder++,
            ];
        }

        if ($lines === []) {
            throw ValidationException::withMessages([
                'lines' => __('crm::quote.messages.deal_quote_failed'),
            ]);
        }

        return $this->create([
            'company_id' => $deal->company_id,
            'deal_id' => $deal->id,
            'currency' => $deal->currency ?? $this->currencyService->defaultCurrency(),
            'issued_at' => now()->toDateString(),
            'expires_at' => now()->addDays(30)->toDateString(),
            'notes' => __('crm::quote.messages.deal_quote_note', ['title' => $deal->title]),
            'lines' => $lines,
        ]);
    }

    public function markAsSent(Quote $quote): Quote
    {
        if ($quote->status === Quote::STATUS_VOID) {
            return $quote;
        }

        if (! in_array($quote->status, [Quote::STATUS_DRAFT, Quote::STATUS_SENT], true)) {
            throw ValidationException::withMessages([
                'status' => __('crm::quote.messages.cannot_send'),
            ]);
        }

        $quote->update(['status' => Quote::STATUS_SENT]);

        return $quote->fresh();
    }

    public function voidQuote(Quote $quote): Quote
    {
        if (in_array($quote->status, [Quote::STATUS_ACCEPTED, Quote::STATUS_VOID], true)) {
            throw ValidationException::withMessages([
                'status' => __('crm::quote.messages.cannot_void'),
            ]);
        }

        $quote->update(['status' => Quote::STATUS_VOID]);

        return $quote->fresh();
    }

    public function deleteQuote(Quote $quote): void
    {
        if ($quote->status === Quote::STATUS_ACCEPTED) {
            throw ValidationException::withMessages([
                'status' => __('crm::quote.messages.cannot_delete_accepted'),
            ]);
        }

        $quote->delete();
    }

    public function refreshExpiry(Quote $quote): Quote
    {
        if ($quote->status === Quote::STATUS_SENT && $quote->isPastExpiry()) {
            $quote->update(['status' => Quote::STATUS_EXPIRED]);

            return $quote->fresh();
        }

        return $quote;
    }

    public function accept(Quote $quote, array $data, ?string $ip = null): Quote
    {
        $quote = $this->refreshExpiry($quote);

        if (! $quote->isRespondable()) {
            throw ValidationException::withMessages([
                'status' => __('crm::quote.messages.not_respondable'),
            ]);
        }

        return DB::transaction(function () use ($quote, $data, $ip) {
            $quote->update([
                'status' => Quote::STATUS_ACCEPTED,
                'responded_at' => now(),
                'responder_name' => $data['responder_name'],
                'responder_email' => $data['responder_email'] ?? null,
                'responder_ip' => $ip,
                'response_note' => $data['response_note'] ?? null,
            ]);

            $project = $this->winDealAndCreateProject($quote);

            if ($project) {
                $quote->update(['project_id' => $project->id]);
            }

            return $quote->fresh(['lines', 'company', 'deal', 'project']);
        });
    }

    public function reject(Quote $quote, array $data, ?string $ip = null): Quote
    {
        $quote = $this->refreshExpiry($quote);

        if (! $quote->isRespondable()) {
            throw ValidationException::withMessages([
                'status' => __('crm::quote.messages.not_respondable'),
            ]);
        }

        $quote->update([
            'status' => Quote::STATUS_REJECTED,
            'responded_at' => now(),
            'responder_name' => $data['responder_name'],
            'responder_email' => $data['responder_email'] ?? null,
            'responder_ip' => $ip,
            'response_note' => $data['response_note'] ?? null,
        ]);

        return $quote->fresh(['lines', 'company', 'deal']);
    }

    public function downloadPdf(Quote $quote): Response
    {
        $quote->load(['company', 'lines.service', 'lines.product', 'deal']);

        $pdf = Pdf::loadView('crm::admin.quote.pdf', [
            'quote' => $quote,
            'companyBranding' => CompanyBranding::forInvoice(),
        ]);

        return $pdf->download($quote->quote_number.'.pdf');
    }

    public function generateQuoteNumber(): string
    {
        $prefix = 'QUO';
        $year = now()->format('Y');

        $lastNumber = Quote::withTrashed()
            ->where('quote_number', 'like', "{$prefix}-{$year}-%")
            ->orderByDesc('id')
            ->value('quote_number');

        $sequence = 1;

        if ($lastNumber && preg_match('/-(\d+)$/', $lastNumber, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    private function winDealAndCreateProject(Quote $quote): ?Project
    {
        $deal = $quote->deal()->with('services')->first();

        if (! $deal) {
            return null;
        }

        $wonStage = PipelineStage::query()
            ->where('is_won', true)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();

        if ($wonStage && $deal->pipeline_stage_id !== $wonStage->id) {
            $this->dealService->moveStage($deal, $wonStage->id, __('crm::quote.messages.accepted_stage_note', [
                'number' => $quote->quote_number,
            ]));
            $deal->refresh();
        } elseif ($deal->status !== Deal::STATUS_WON) {
            $deal->update([
                'status' => Deal::STATUS_WON,
                'won_at' => now(),
                'closed_at' => now(),
                'lost_at' => null,
                'lost_reason' => null,
                'probability' => 100,
            ]);
        }

        $project = Project::query()->where('deal_id', $deal->id)->first()
            ?? Project::createFromDeal($deal->fresh(['services']));

        if (! $project) {
            return null;
        }

        $serviceIds = $quote->lines()
            ->where('item_type', QuoteLine::TYPE_SERVICE)
            ->whereNotNull('service_id')
            ->pluck('service_id')
            ->unique()
            ->values()
            ->all();

        if ($serviceIds !== []) {
            $project->services()->sync($serviceIds);
        }

        $project->update([
            'budget' => $quote->total,
            'currency' => $quote->currency,
            'budget_exchange_rate' => $this->currencyService->snapshotRateToBase($quote->currency),
        ]);

        return $project->fresh();
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @return array<int, array<string, mixed>>
     */
    private function normalizeLines(array $lines): array
    {
        $normalized = [];

        foreach (array_values($lines) as $index => $line) {
            $itemType = $line['item_type'] ?? null;
            $serviceId = ! empty($line['service_id']) ? (int) $line['service_id'] : null;
            $productId = ! empty($line['product_id']) ? (int) $line['product_id'] : null;

            if ($itemType === QuoteLine::TYPE_SERVICE) {
                $productId = null;
            } elseif ($itemType === QuoteLine::TYPE_PRODUCT) {
                $serviceId = null;
            }

            if (! $serviceId && ! $productId) {
                throw ValidationException::withMessages([
                    "lines.{$index}.item_type" => __('crm::quote.messages.line_item_required'),
                ]);
            }

            $description = trim((string) ($line['description'] ?? ''));

            if ($description === '') {
                if ($serviceId) {
                    $service = Service::query()->find($serviceId);
                    $description = $service?->getTranslation('title', app()->getLocale()) ?? 'Service';
                } elseif ($productId) {
                    $product = Product::query()->find($productId);
                    $description = $product?->name ?? 'Product';
                }
            }

            $quantity = max(1, (int) ($line['quantity'] ?? 1));
            $unitPrice = (float) ($line['unit_price'] ?? 0);
            $discountPercent = (float) ($line['discount_percent'] ?? 0);
            $taxPercent = (float) ($line['tax_percent'] ?? 0);
            $amounts = QuoteLine::calculateAmounts($quantity, $unitPrice, $discountPercent, $taxPercent);

            $normalized[] = [
                'item_type' => $serviceId ? QuoteLine::TYPE_SERVICE : QuoteLine::TYPE_PRODUCT,
                'service_id' => $serviceId,
                'product_id' => $productId,
                'description' => $description,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount_percent' => $discountPercent,
                'tax_percent' => $taxPercent,
                'discount_amount' => $amounts['discount_amount'],
                'tax_amount' => $amounts['tax_amount'],
                'amount' => $amounts['amount'],
                'sort_order' => $line['sort_order'] ?? $index,
            ];
        }

        if ($normalized === []) {
            throw ValidationException::withMessages([
                'lines' => __('crm::quote.messages.lines_required'),
            ]);
        }

        return $normalized;
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @return array{subtotal: float, discount_amount: float, tax_amount: float, total: float}
     */
    private function sumTotals(array $lines): array
    {
        $subtotal = 0.0;
        $discountAmount = 0.0;
        $taxAmount = 0.0;
        $total = 0.0;

        foreach ($lines as $line) {
            $base = round((int) $line['quantity'] * (float) $line['unit_price'], 2);
            $subtotal += $base;
            $discountAmount += (float) $line['discount_amount'];
            $taxAmount += (float) $line['tax_amount'];
            $total += (float) $line['amount'];
        }

        return [
            'subtotal' => round($subtotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'tax_amount' => round($taxAmount, 2),
            'total' => round($total, 2),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     */
    private function syncLines(Quote $quote, array $lines): void
    {
        $quote->lines()->delete();

        foreach ($lines as $line) {
            QuoteLine::query()->create([
                'quote_id' => $quote->id,
                'service_id' => $line['service_id'],
                'product_id' => $line['product_id'],
                'item_type' => $line['item_type'],
                'description' => $line['description'],
                'quantity' => $line['quantity'],
                'unit_price' => $line['unit_price'],
                'discount_percent' => $line['discount_percent'],
                'tax_percent' => $line['tax_percent'],
                'discount_amount' => $line['discount_amount'],
                'tax_amount' => $line['tax_amount'],
                'amount' => $line['amount'],
                'sort_order' => $line['sort_order'],
            ]);
        }
    }
}

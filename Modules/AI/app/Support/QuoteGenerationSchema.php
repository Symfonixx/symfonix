<?php

namespace Modules\AI\Support;

use Modules\CRM\Models\QuoteLine;

class QuoteGenerationSchema
{
    /**
     * @param  array<string, mixed>  $context
     */
    public static function systemPrompt(string $locale, array $context): string
    {
        $issuer = is_string($context['issuer_name'] ?? null) && $context['issuer_name'] !== ''
            ? $context['issuer_name']
            : (string) config('app.name', 'Symfonix');
        $customer = is_string($context['customer_name'] ?? null) && $context['customer_name'] !== ''
            ? $context['customer_name']
            : 'the customer';

        $prompt = 'You draft a commercial quote that becomes a binding service contract when the customer accepts it. '
            .'Write human-readable copy in locale "'.$locale.'". '
            .'The provider (us) is "'.$issuer.'". The customer is "'.$customer.'". '
            .'Return ONLY valid JSON with these keys:'."\n"
            .json_encode([
                'terms' => 'Plain-text contract terms (not HTML) covering: parties; scope and deliverables tied to the deal and line items; fees and payment; timeline; revisions/change requests; intellectual property; confidentiality; acceptance (accepting this quote forms the agreement); termination; limitation of liability. 2500-8000 characters. Do not invent awards, jurisdictions, or legal registrations that contradict the company profile. If the customer country is known, you may mention it as the governing place of business; otherwise omit a specific court.',
                'notes' => 'Short cover note to the customer (2-5 sentences) summarizing the engagement. Max 1500 characters.',
                'validity_days' => 'Integer 7-90. How many days the quote stays open. Default 30.',
                'currency' => '3-letter ISO currency code matching the deal unless extra instructions say otherwise.',
                'lines' => 'Array of 1-12 line items. Each object: item_type (service|product), service_id (int or null), product_id (int or null), description (plain text, max 255 chars, specific to this customer/deal), quantity (int >= 1), unit_price (number >= 0), discount_percent (0-100), tax_percent (0-100). Use ONLY catalog IDs from the brief. Prefer attached deal services when present. If the deal has a value, line totals should approximately match it unless extra instructions override. Do not invent catalog items.',
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n"
            .'Match the provider voice from the company profile. Do not wrap the JSON in markdown fences.';

        return CompanyContentProfile::appendTo($prompt);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public static function userMessage(array $context, ?string $prompt = null): string
    {
        $payload = $context;
        unset($payload['issuer_name'], $payload['customer_name']);

        $message = "Draft the quote/contract from this company, deal, and catalog:\n"
            .json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        if (is_string($prompt) && trim($prompt) !== '') {
            $message .= "\n\nExtra instructions from the sales user:\n".trim($prompt);
        }

        return $message;
    }

    /**
     * @param  array<string, mixed>  $raw
     * @param  array{services: array<int, array<string, mixed>>, products: array<int, array<string, mixed>>}  $catalog
     * @param  list<array<string, mixed>>  $fallbackLines
     * @return array{terms: string, notes: string, validity_days: int, currency: string, lines: list<array<string, mixed>>}
     */
    public static function normalize(array $raw, array $catalog, array $fallbackLines, string $dealCurrency, float $defaultTax): array
    {
        $currency = strtoupper(trim((string) ($raw['currency'] ?? '')));
        if (! preg_match('/^[A-Z]{3}$/', $currency)) {
            $currency = strtoupper($dealCurrency);
        }

        $validityDays = (int) ($raw['validity_days'] ?? 30);
        $validityDays = max(7, min(90, $validityDays > 0 ? $validityDays : 30));

        $lines = self::normalizeLines($raw['lines'] ?? [], $catalog, $defaultTax);

        if ($lines === []) {
            $lines = self::normalizeLines($fallbackLines, $catalog, $defaultTax);
        }

        return [
            'terms' => self::plainText($raw['terms'] ?? '', 10000),
            'notes' => self::plainText($raw['notes'] ?? '', 5000),
            'validity_days' => $validityDays,
            'currency' => $currency !== '' ? $currency : strtoupper($dealCurrency),
            'lines' => $lines,
        ];
    }

    /**
     * @param  array{services: array<int, array<string, mixed>>, products: array<int, array<string, mixed>>}  $catalog
     * @return list<array<string, mixed>>
     */
    private static function normalizeLines(mixed $rawLines, array $catalog, float $defaultTax): array
    {
        if (! is_array($rawLines)) {
            return [];
        }

        $services = $catalog['services'] ?? [];
        $products = $catalog['products'] ?? [];
        $normalized = [];

        foreach (array_values($rawLines) as $line) {
            if (! is_array($line)) {
                continue;
            }

            $item = self::normalizeLine($line, $services, $products, $defaultTax);
            if ($item === null) {
                continue;
            }

            $normalized[] = $item;

            if (count($normalized) >= 12) {
                break;
            }
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $line
     * @param  array<int, array<string, mixed>>  $services
     * @param  array<int, array<string, mixed>>  $products
     * @return array<string, mixed>|null
     */
    private static function normalizeLine(array $line, array $services, array $products, float $defaultTax): ?array
    {
        $itemType = strtolower(trim((string) ($line['item_type'] ?? QuoteLine::TYPE_SERVICE)));
        if (! in_array($itemType, [QuoteLine::TYPE_SERVICE, QuoteLine::TYPE_PRODUCT], true)) {
            $itemType = QuoteLine::TYPE_SERVICE;
        }

        $serviceId = self::positiveInt($line['service_id'] ?? null);
        $productId = self::positiveInt($line['product_id'] ?? null);

        if ($itemType === QuoteLine::TYPE_SERVICE) {
            $productId = null;
            if ($serviceId === null || ! isset($services[$serviceId])) {
                $serviceId = self::matchCatalogId($line, $services, ['title', 'name']);
            }
            if ($serviceId === null) {
                return null;
            }
        } else {
            $serviceId = null;
            if ($productId === null || ! isset($products[$productId])) {
                $productId = self::matchCatalogId($line, $products, ['name', 'title']);
            }
            if ($productId === null) {
                return null;
            }
        }

        $quantity = max(1, (int) ($line['quantity'] ?? 1));
        $unitPrice = round(max(0, (float) ($line['unit_price'] ?? 0)), 2);
        $discountPercent = round(max(0, min(100, (float) ($line['discount_percent'] ?? 0))), 2);
        $taxPercent = $line['tax_percent'] ?? null;

        if ($taxPercent === null || $taxPercent === '') {
            if ($itemType === QuoteLine::TYPE_PRODUCT) {
                $taxPercent = (float) ($products[$productId]['tax_percent'] ?? $defaultTax);
            } else {
                $taxPercent = $defaultTax;
            }
        }

        $taxPercent = round(max(0, min(100, (float) $taxPercent)), 2);

        $description = self::plainText($line['description'] ?? '', 255);
        if ($description === '') {
            if ($itemType === QuoteLine::TYPE_SERVICE) {
                $description = self::plainText((string) ($services[$serviceId]['title'] ?? 'Service'), 255);
            } else {
                $description = self::plainText((string) ($products[$productId]['name'] ?? 'Product'), 255);
            }
        }

        return [
            'item_type' => $itemType,
            'service_id' => $serviceId,
            'product_id' => $productId,
            'description' => $description,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_percent' => $discountPercent,
            'tax_percent' => $taxPercent,
        ];
    }

    /**
     * @param  array<string, mixed>  $line
     * @param  array<int, array<string, mixed>>  $catalog
     * @param  list<string>  $keys
     */
    private static function matchCatalogId(array $line, array $catalog, array $keys): ?int
    {
        $needles = [];
        foreach (['description', 'title', 'name'] as $field) {
            $value = self::plainText((string) ($line[$field] ?? ''), 255);
            if ($value !== '') {
                $needles[] = mb_strtolower($value);
            }
        }

        if ($needles === []) {
            return null;
        }

        foreach ($catalog as $id => $item) {
            foreach ($keys as $key) {
                $label = mb_strtolower(self::plainText((string) ($item[$key] ?? ''), 255));
                if ($label === '') {
                    continue;
                }

                foreach ($needles as $needle) {
                    if ($label === $needle || str_contains($needle, $label) || str_contains($label, $needle)) {
                        return (int) $id;
                    }
                }
            }
        }

        return null;
    }

    private static function positiveInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $id = (int) $value;

        return $id > 0 ? $id : null;
    }

    private static function plainText(mixed $value, int $max): string
    {
        if (is_array($value)) {
            $value = implode("\n", array_filter(array_map(
                static fn (mixed $part): string => is_scalar($part) ? trim((string) $part) : '',
                $value,
            )));
        }

        if (! is_scalar($value)) {
            return '';
        }

        $text = trim(html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $text = preg_replace("/\R{3,}/", "\n\n", $text) ?? $text;

        if (mb_strlen($text) > $max) {
            $text = rtrim(mb_substr($text, 0, $max));
        }

        return $text;
    }
}

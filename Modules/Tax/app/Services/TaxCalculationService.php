<?php

namespace Modules\Tax\Services;

use Modules\Tax\Models\TaxRate;

class TaxCalculationService
{
    /**
     * @return array{subtotal: float, tax_percent: float, tax_amount: float, amount: float, base: float}
     */
    public function calculateLine(
        int $quantity,
        float $unitPrice,
        ?TaxRate $taxRate = null,
        float $discountPercent = 0,
        ?float $taxPercentOverride = null,
    ): array {
        $base = round($quantity * $unitPrice, 2);
        $discountAmount = round($base * (max(0, min(100, $discountPercent)) / 100), 2);
        $taxable = round($base - $discountAmount, 2);

        $taxPercent = $taxPercentOverride ?? ($taxRate ? (float) $taxRate->percentage : 0.0);

        if ($taxPercent <= 0) {
            return [
                'base' => $base,
                'subtotal' => $taxable,
                'tax_percent' => 0.0,
                'tax_amount' => 0.0,
                'amount' => $taxable,
            ];
        }

        if ($taxRate?->isInclusive()) {
            $taxAmount = round($taxable * ($taxPercent / (100 + $taxPercent)), 2);
            $subtotal = round($taxable - $taxAmount, 2);

            return [
                'base' => $base,
                'subtotal' => $subtotal,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'amount' => $taxable,
            ];
        }

        $taxAmount = round($taxable * ($taxPercent / 100), 2);

        return [
            'base' => $base,
            'subtotal' => $taxable,
            'tax_percent' => $taxPercent,
            'tax_amount' => $taxAmount,
            'amount' => round($taxable + $taxAmount, 2),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @return array{subtotal: float, tax_amount: float, total: float, lines: array<int, array<string, mixed>>}
     */
    public function calculateDocument(array $lines, ?TaxRate $headerTaxRate = null): array
    {
        $normalizedLines = [];
        $subtotal = 0.0;
        $taxAmount = 0.0;

        foreach ($lines as $index => $line) {
            $quantity = (int) ($line['quantity'] ?? 1);
            $unitPrice = (float) ($line['unit_price'] ?? 0);
            $discountPercent = (float) ($line['discount_percent'] ?? 0);
            $taxRate = $this->resolveLineTaxRate($line, $headerTaxRate);
            $taxPercentOverride = isset($line['tax_percent']) ? (float) $line['tax_percent'] : null;

            $amounts = $this->calculateLine(
                $quantity,
                $unitPrice,
                $taxRate,
                $discountPercent,
                $taxPercentOverride,
            );

            $normalizedLines[] = array_merge($line, [
                'tax_rate_id' => $taxRate?->id ?? ($line['tax_rate_id'] ?? null),
                'tax_percent' => $amounts['tax_percent'],
                'tax_amount' => $amounts['tax_amount'],
                'amount' => $amounts['amount'],
                'sort_order' => $line['sort_order'] ?? $index,
            ]);

            $subtotal += $amounts['subtotal'];
            $taxAmount += $amounts['tax_amount'];
        }

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'total' => round($subtotal + $taxAmount, 2),
            'lines' => $normalizedLines,
        ];
    }

    /**
     * @param  array<string, mixed>  $line
     */
    private function resolveLineTaxRate(array $line, ?TaxRate $headerTaxRate): ?TaxRate
    {
        if (! empty($line['tax_rate_id'])) {
            return TaxRate::query()->active()->find((int) $line['tax_rate_id']);
        }

        return $headerTaxRate;
    }
}

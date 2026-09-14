<?php

namespace Modules\Tax\Services;

use Illuminate\Support\Collection;
use Modules\Tax\Models\TaxLedgerEntry;
use Modules\Tax\Models\TaxRate;

class TaxFilingReportService
{
    /**
     * @return array{
     *     from: string,
     *     to: string,
     *     output_tax: float,
     *     input_tax: float,
     *     net_tax_payable: float,
     *     by_rate: Collection,
     *     entries: Collection
     * }
     */
    public function generate(string $from, string $to, ?int $taxRateId = null, ?int $companyId = null): array
    {
        $query = TaxLedgerEntry::query()
            ->with(['taxRate:id,name,percentage', 'company:id,name', 'project:id,title'])
            ->betweenDates($from, $to)
            ->orderByDesc('transaction_date');

        if ($taxRateId) {
            $query->where('tax_rate_id', $taxRateId);
        }

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $entries = $query->get();

        $outputTax = round((float) $entries->where('direction', TaxLedgerEntry::DIRECTION_OUTPUT)->sum('base_amount'), 2);
        $inputTax = round((float) $entries->where('direction', TaxLedgerEntry::DIRECTION_INPUT)->sum('base_amount'), 2);

        $byRate = $entries
            ->groupBy('tax_rate_id')
            ->map(function (Collection $group, $rateId) {
                $rate = $rateId ? TaxRate::query()->find($rateId) : null;

                return [
                    'tax_rate_id' => $rateId,
                    'tax_rate_name' => $rate?->name ?? __('tax::report.unassigned_rate'),
                    'percentage' => $rate?->percentage,
                    'output_tax' => round((float) $group->where('direction', TaxLedgerEntry::DIRECTION_OUTPUT)->sum('base_amount'), 2),
                    'input_tax' => round((float) $group->where('direction', TaxLedgerEntry::DIRECTION_INPUT)->sum('base_amount'), 2),
                    'net_tax' => round(
                        (float) $group->where('direction', TaxLedgerEntry::DIRECTION_OUTPUT)->sum('base_amount')
                        - (float) $group->where('direction', TaxLedgerEntry::DIRECTION_INPUT)->sum('base_amount'),
                        2
                    ),
                ];
            })
            ->values();

        return [
            'from' => $from,
            'to' => $to,
            'output_tax' => $outputTax,
            'input_tax' => $inputTax,
            'net_tax_payable' => round($outputTax - $inputTax, 2),
            'by_rate' => $byRate,
            'entries' => $entries,
        ];
    }
}

<?php

namespace Modules\Reporting\Services;

use Illuminate\Support\Facades\DB;
use Modules\Finance\Models\Commission;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;
use Modules\Finance\Models\Salary;
use Modules\Finance\Services\FinanceService;
use Modules\Reporting\DTOs\ReportFilters;
use Modules\Tax\Services\TaxFilingReportService;

class FinanceReportService extends BaseReportService
{
    public function __construct(
        private readonly FinanceService $financeService,
        private readonly TaxFilingReportService $taxReportService,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $resolved = $this->resolveFilters($filters);
        $dateRange = $resolved->dateRange();
        $previousRange = [
            'from' => $resolved->previousStart->toDateString(),
            'to' => $resolved->previousEnd->toDateString(),
        ];

        $revenue = $this->financeService->getTotalIncome($dateRange);
        $expenses = $this->financeService->getTotalExpenses($dateRange);
        $prevRevenue = $this->financeService->getTotalIncome($previousRange);
        $prevExpenses = $this->financeService->getTotalExpenses($previousRange);

        $arAging = $this->financeService->getAccountsReceivableAging($resolved->currency);
        $taxReport = $this->taxReportService->generate($dateRange['from'], $dateRange['to']);
        $apTotal = $this->accountsPayableTotal();

        return [
            'filters' => $resolved->toArray(),
            'currency' => $resolved->currency,
            'chart_colors' => $this->chartColors(),
            'kpis' => [
                'revenue' => $this->kpiMetric($revenue, $prevRevenue),
                'expenses' => $this->kpiMetric($expenses, $prevExpenses),
                'profit' => $this->kpiMetric($revenue - $expenses, $prevRevenue - $prevExpenses),
                'accounts_receivable' => [
                    'value' => $arAging['total_outstanding'],
                    'previous' => $arAging['total_outstanding'],
                    'change' => null,
                    'trend' => 'flat',
                ],
                'accounts_payable' => [
                    'value' => $apTotal,
                    'previous' => $apTotal,
                    'change' => null,
                    'trend' => 'flat',
                ],
                'net_tax' => [
                    'value' => $taxReport['net_tax_payable'],
                    'previous' => $taxReport['net_tax_payable'],
                    'change' => null,
                    'trend' => 'flat',
                ],
            ],
            'charts' => [
                'monthly_trend' => $this->monthlyTrend($resolved),
                'expense_breakdown' => $this->expenseBreakdown($resolved),
                'ar_aging' => [
                    'labels' => [
                        __('reporting::report.ar.current'),
                        __('reporting::report.ar.days_1_30'),
                        __('reporting::report.ar.days_31_60'),
                        __('reporting::report.ar.days_61_90'),
                        __('reporting::report.ar.over_90'),
                    ],
                    'values' => array_values($arAging['buckets']),
                ],
                'tax_summary' => [
                    'output' => $taxReport['output_tax'],
                    'input' => $taxReport['input_tax'],
                    'net' => $taxReport['net_tax_payable'],
                ],
            ],
            'tables' => [
                'expense_categories' => $this->expenseBreakdown($resolved),
                'tax_by_rate' => $taxReport['by_rate']->toArray(),
                'ar_invoices' => $arAging['invoices']->take(10)->map(fn ($inv) => [
                    'number' => $inv->invoice_number,
                    'company' => $inv->company?->name,
                    'total' => (float) $inv->total,
                    'due_at' => $inv->due_at?->toDateString(),
                    'status' => $inv->status,
                ])->values()->all(),
            ],
        ];
    }

    /**
     * @return array<int, array{label: string, revenue: float, expenses: float, profit: float}>
     */
    private function monthlyTrend(ReportFilters $filters): array
    {
        $entries = JournalEntry::query()
            ->select(['flow', 'amount', 'currency', 'exchange_rate', 'base_amount', 'transaction_date'])
            ->whereBetween('transaction_date', [$filters->start, $filters->end])
            ->orderBy('transaction_date')
            ->get();

        $grouped = [];

        foreach ($entries as $entry) {
            $key = $entry->transaction_date->format('Y-m');

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'label' => $entry->transaction_date->translatedFormat('M Y'),
                    'revenue' => 0.0,
                    'expenses' => 0.0,
                ];
            }

            $amount = (float) ($entry->base_amount ?: $entry->amount);

            if ($entry->flow === JournalEntry::FLOW_REVENUE) {
                $grouped[$key]['revenue'] += $amount;
            } else {
                $grouped[$key]['expenses'] += $amount;
            }
        }

        return collect($grouped)
            ->map(fn (array $row) => [
                'label' => $row['label'],
                'revenue' => round($row['revenue'], 2),
                'expenses' => round($row['expenses'], 2),
                'profit' => round($row['revenue'] - $row['expenses'], 2),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{category: string, amount: float}>
     */
    private function expenseBreakdown(ReportFilters $filters): array
    {
        return JournalLine::query()
            ->select([
                'expense_category_id',
                DB::raw('SUM(journal_lines.amount) as total'),
            ])
            ->where('side', JournalLine::SIDE_DEBIT)
            ->where('account', JournalLine::ACCOUNT_EXPENSE)
            ->whereHas('journalEntry', fn ($q) => $q
                ->expense()
                ->whereBetween('transaction_date', [$filters->start, $filters->end]))
            ->with('expenseCategory:id,name')
            ->groupBy('expense_category_id')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'category' => $row->expenseCategory?->name ?? __('reporting::report.uncategorized'),
                'amount' => round((float) $row->total, 2),
            ])
            ->values()
            ->all();
    }

    private function accountsPayableTotal(): float
    {
        $pendingSalaries = (float) Salary::query()->where('status', Salary::STATUS_PENDING)->sum('base_salary');
        $pendingCommissions = (float) Commission::query()->where('status', Commission::STATUS_PENDING)->sum('commission_amount');

        return round($pendingSalaries + $pendingCommissions, 2);
    }
}

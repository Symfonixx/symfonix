<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Carbon\CarbonInterface;
use Modules\AI\Support\CostLimiter;
use Modules\AI\Support\ToolResult;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Services\CurrencyService;
use Modules\Finance\Services\FinanceService;

class GetPaymentStatsTool extends AbstractAssistantTool
{
    public function __construct(
        CostLimiter $limiter,
        private readonly FinanceService $financeService,
        private readonly CurrencyService $currencyService,
    ) {
        parent::__construct($limiter);
    }

    public function name(): string
    {
        return 'get_payment_stats';
    }

    public function description(): string
    {
        return 'Get sales/revenue, profit, outstanding receivables, paid invoice totals, and growth rates (period-over-period and year-over-year). Use this for "how much did we sell", معدل النمو, growth rate, or this year vs last year.';
    }

    public function parameters(): array
    {
        return $this->periodParameters();
    }

    public function permissions(): array
    {
        return ['finance.dashboard.view'];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $range = $this->period($arguments);
        $currency = $this->currencyService->displayCurrency();

        $currentRevenue = $this->incomeBetween($range['start']->toDateString(), $range['end']->toDateString());
        $previousRevenue = $this->incomeBetween(
            $range['previous_start']->toDateString(),
            $range['previous_end']->toDateString()
        );
        $samePeriodLastYearRevenue = $this->incomeBetween(
            $range['start']->copy()->subYear()->toDateString(),
            $range['end']->copy()->subYear()->toDateString()
        );

        $thisYearFrom = now()->startOfYear()->toDateString();
        $thisYearTo = now()->toDateString();
        $lastYearFrom = now()->copy()->subYear()->startOfYear()->toDateString();
        $lastYearYtdTo = now()->copy()->subYear()->toDateString();
        $lastYearFullTo = now()->copy()->subYear()->endOfYear()->toDateString();

        $thisYearRevenue = $this->incomeBetween($thisYearFrom, $thisYearTo);
        $lastYearYtdRevenue = $this->incomeBetween($lastYearFrom, $lastYearYtdTo);
        $lastYearFullRevenue = $this->incomeBetween($lastYearFrom, $lastYearFullTo);

        $outstanding = Invoice::query()->open()->selectRaw('COALESCE(SUM(total), 0) as value')->value('value');
        $paidCurrent = $this->paidInvoicesBetween($range['start'], $range['end']);
        $paidThisYear = $this->paidInvoicesBetween(now()->startOfYear(), now());
        $paidLastYearYtd = $this->paidInvoicesBetween(now()->copy()->subYear()->startOfYear(), now()->copy()->subYear());
        $paidLastYearFull = $this->paidInvoicesBetween(
            now()->copy()->subYear()->startOfYear(),
            now()->copy()->subYear()->endOfYear()
        );

        $yearOverYear = $this->growthMetric($thisYearRevenue, $lastYearYtdRevenue);
        if ($yearOverYear['growth_rate_percent'] === null) {
            $yearOverYear = $this->growthMetric(
                (float) $paidThisYear['value'],
                (float) $paidLastYearYtd['value']
            );
            $yearOverYear['source'] = 'paid_invoices';
        } else {
            $yearOverYear['source'] = 'journal_revenue';
        }

        $data = [
            'period' => $range['period'],
            'period_label' => $range['source_label'],
            'currency' => $currency,
            'revenue' => round($currentRevenue, 2),
            'profit' => round($currentRevenue - $this->financeService->getTotalExpenses([
                'from' => $range['start']->toDateString(),
                'to' => $range['end']->toDateString(),
            ]), 2),
            'outstanding' => round((float) $outstanding, 2),
            'paid_invoices_count' => (int) $paidCurrent['count'],
            'paid_invoices_total' => round((float) $paidCurrent['value'], 2),
            'growth' => [
                'vs_previous_period' => array_merge(
                    $this->growthMetric($currentRevenue, $previousRevenue),
                    [
                        'previous_label' => $range['previous_start']->toDateString().' – '.$range['previous_end']->toDateString(),
                    ]
                ),
                'vs_same_period_last_year' => array_merge(
                    $this->growthMetric($currentRevenue, $samePeriodLastYearRevenue),
                    [
                        'previous_label' => $range['start']->copy()->subYear()->toDateString().' – '.$range['end']->copy()->subYear()->toDateString(),
                    ]
                ),
                'this_year_vs_last_year_ytd' => array_merge($yearOverYear, [
                    'this_year' => round($thisYearRevenue, 2),
                    'last_year_ytd' => round($lastYearYtdRevenue, 2),
                    'last_year_full' => round($lastYearFullRevenue, 2),
                    'this_year_paid_invoices' => round((float) $paidThisYear['value'], 2),
                    'last_year_ytd_paid_invoices' => round((float) $paidLastYearYtd['value'], 2),
                    'last_year_full_paid_invoices' => round((float) $paidLastYearFull['value'], 2),
                    'note' => $yearOverYear['growth_rate_percent'] === null
                        ? 'Growth rate is not defined when the comparison period has zero revenue. Totals are still included.'
                        : 'Year-over-year growth compares this year to date with the same dates last year. last_year_full is the complete previous calendar year.',
                ]),
            ],
        ];

        return ToolResult::success($data, ['Finance', $range['source_label'], 'Year-over-year']);
    }

    private function incomeBetween(string $from, string $to): float
    {
        return $this->financeService->getTotalIncome([
            'from' => $from,
            'to' => $to,
        ]);
    }

    /**
     * @return array{count: int, value: float}
     */
    private function paidInvoicesBetween(CarbonInterface $start, CarbonInterface $end): array
    {
        $row = Invoice::query()
            ->where('status', Invoice::STATUS_PAID)
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as value')
            ->first();

        return [
            'count' => (int) ($row->count ?? 0),
            'value' => (float) ($row->value ?? 0),
        ];
    }
}

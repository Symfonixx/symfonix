<?php

namespace Modules\Finance\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Finance\Services\FinanceService;

class FinancialDashboard extends Component
{
    /** @var array<int, string> */
    public array $selectedMonths = [];

    public float $totalRevenue = 0;

    public float $totalExpenses = 0;

    public float $lifetimeProfit = 0;

    public float $totalLosses = 0;

    public float $mrr = 0;

    public float $arr = 0;

    public int $activeSubscriptions = 0;

    public string $saasCurrency = 'USD';

    public string $displayCurrency = 'USD';

    /** @var array<int, array{label: string, profit: float, expenses: float, losses: float, revenue: float}> */
    public array $chartData = [];

    /** @var array<int, array{label: string, month: int, revenue: float, expenses: float}> */
    public array $trendChartData = [];

    /** @var array<int, int> */
    public array $availableYears = [];

    public int $selectedYear;

    public function mount(FinanceService $financeService): void
    {
        $this->selectedYear = (int) now()->year;
        $this->displayCurrency = app(\Modules\Finance\Services\CurrencyService::class)->displayCurrency();
        $this->refreshMetrics($financeService);
    }

    public function updatedSelectedMonths(FinanceService $financeService): void
    {
        $this->refreshMetrics($financeService);
    }

    public function clearMonthFilter(FinanceService $financeService): void
    {
        $this->selectedMonths = [];
        $this->refreshMetrics($financeService);
        $this->dispatch('finance-month-filter-cleared');
    }

    public function updatedSelectedYear(FinanceService $financeService): void
    {
        $this->trendChartData = $financeService->getMonthlyTrendForYear($this->selectedYear);
    }

    #[On('transaction-logged')]
    public function refreshMetrics(FinanceService $financeService): void
    {
        $monthKeys = $this->selectedMonths ?: null;
        $summary = $financeService->getMetricsSummary($monthKeys);

        $this->totalRevenue = $summary['revenue'];
        $this->totalExpenses = $summary['expenses'];
        $this->lifetimeProfit = $summary['profit'];
        $this->totalLosses = $summary['losses'];

        $saas = $financeService->getSubscriptionMetrics();
        $this->mrr = $saas['mrr'];
        $this->arr = $saas['arr'];
        $this->activeSubscriptions = $saas['active_count'];
        $this->saasCurrency = $saas['primary_currency'];
        $this->displayCurrency = app(\Modules\Finance\Services\CurrencyService::class)->displayCurrency();

        $this->chartData = $financeService->getMonthlyChartData($monthKeys);

        $this->availableYears = $financeService->getAvailableYears();

        if (! in_array($this->selectedYear, $this->availableYears, true)) {
            $this->selectedYear = $this->availableYears[0] ?? (int) now()->year;
        }

        $this->trendChartData = $financeService->getMonthlyTrendForYear($this->selectedYear);
    }

    public function render(FinanceService $financeService)
    {
        return view('finance::livewire.financial-dashboard', [
            'availableMonths' => $financeService->getAvailableMonths(),
            'isLifetimeView' => empty($this->selectedMonths),
        ]);
    }
}

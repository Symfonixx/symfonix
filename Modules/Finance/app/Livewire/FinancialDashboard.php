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

    public function mount(FinanceService $financeService): void
    {
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

    #[On('transaction-logged')]
    public function refreshMetrics(FinanceService $financeService): void
    {
        $monthKeys = $this->selectedMonths ?: null;
        $summary = $financeService->getMetricsSummary($monthKeys);

        $this->totalRevenue = $summary['revenue'];
        $this->totalExpenses = $summary['expenses'];
        $this->lifetimeProfit = $summary['profit'];
        $this->totalLosses = $summary['losses'];
    }

    public function render(FinanceService $financeService)
    {
        $monthKeys = $this->selectedMonths ?: null;

        return view('finance::livewire.financial-dashboard', [
            'availableMonths' => $financeService->getAvailableMonths(),
            'chartData' => $financeService->getMonthlyChartData($monthKeys),
            'isLifetimeView' => empty($this->selectedMonths),
        ]);
    }
}

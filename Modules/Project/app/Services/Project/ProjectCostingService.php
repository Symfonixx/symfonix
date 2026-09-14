<?php

namespace Modules\Project\Services\Project;

use Illuminate\Support\Collection;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\Salary;
use Modules\Finance\Services\CurrencyService;
use Modules\Finance\Services\FinanceService;
use Modules\Project\Models\Project;
use Modules\Project\Models\ProjectEmployee;

class ProjectCostingService
{
    public function __construct(
        private readonly FinanceService $financeService,
    ) {}

    public function workingDaysPerMonth(): int
    {
        return max(1, (int) config('project.working_days_per_month', 24));
    }

    public function dailyRateForEmployee(int $employeeId): float
    {
        $baseSalary = $this->latestBaseSalary($employeeId);

        if ($baseSalary <= 0) {
            return 0.0;
        }

        return round($baseSalary / $this->workingDaysPerMonth(), 2);
    }

    public function latestBaseSalary(int $employeeId): float
    {
        return $this->latestBaseSalaries([$employeeId])[$employeeId] ?? 0.0;
    }

    /**
     * @param  list<int>  $employeeIds
     * @return array<int, float>
     */
    public function latestBaseSalaries(array $employeeIds): array
    {
        $employeeIds = array_values(array_unique(array_filter($employeeIds)));

        if ($employeeIds === []) {
            return [];
        }

        return Salary::query()
            ->whereIn('employee_id', $employeeIds)
            ->orderByDesc('period')
            ->orderByDesc('id')
            ->get(['employee_id', 'base_salary'])
            ->unique('employee_id')
            ->mapWithKeys(fn (Salary $salary) => [
                (int) $salary->employee_id => round((float) $salary->base_salary, 2),
            ])
            ->all();
    }

    public function laborCostForAssignment(ProjectEmployee $assignment): float
    {
        $dailyRate = $this->dailyRateForEmployee((int) $assignment->employee_id);
        $days = $assignment->daysWorked();

        return round($dailyRate * $days, 2);
    }

    /**
     * @return array{
     *     assignments: Collection<int, array{
     *         assignment: ProjectEmployee,
     *         days_worked: int,
     *         base_salary: float,
     *         daily_rate: float,
     *         labor_cost: float,
     *         is_active: bool
     *     }>,
     *     labor_cost: float
     * }
     */
    public function getAssignmentCostBreakdown(Project $project): array
    {
        $project->loadMissing(['assignments.employee']);

        $salaries = $this->latestBaseSalaries(
            $project->assignments->pluck('employee_id')->map(fn ($id) => (int) $id)->unique()->all()
        );

        $assignments = $project->assignments
            ->sortByDesc(fn (ProjectEmployee $row) => $row->started_at?->timestamp ?? 0)
            ->values()
            ->map(function (ProjectEmployee $assignment) use ($salaries) {
                $baseSalary = $salaries[(int) $assignment->employee_id] ?? 0.0;
                $dailyRate = $baseSalary > 0
                    ? round($baseSalary / $this->workingDaysPerMonth(), 2)
                    : 0.0;
                $daysWorked = $assignment->daysWorked();

                return [
                    'assignment' => $assignment,
                    'days_worked' => $daysWorked,
                    'base_salary' => $baseSalary,
                    'daily_rate' => $dailyRate,
                    'labor_cost' => round($dailyRate * $daysWorked, 2),
                    'is_active' => $assignment->isActive(),
                ];
            });

        return [
            'assignments' => $assignments,
            'labor_cost' => round((float) $assignments->sum('labor_cost'), 2),
        ];
    }

    public function sumProjectExpenses(Project $project): float
    {
        return round((float) JournalEntry::query()
            ->expense()
            ->where('reference_type', Project::class)
            ->where('reference_id', $project->id)
            ->sum('amount'), 2);
    }

    /**
     * @return Collection<int, JournalEntry>
     */
    public function getProjectExpenseEntries(Project $project): Collection
    {
        return JournalEntry::query()
            ->expense()
            ->with('lines.expenseCategory')
            ->where('reference_type', Project::class)
            ->where('reference_id', $project->id)
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * P&L is based on invoiced amount (revenue) vs labor + project expenses (cost).
     *
     * @return array{
     *     currency: string,
     *     working_days_per_month: int,
     *     invoiced: float,
     *     labor_cost: float,
     *     expenses: float,
     *     total_cost: float,
     *     profit_or_loss: float,
     *     is_profit: bool,
     *     is_loss: bool,
     *     margin_rate: float,
     *     assignments: Collection,
     *     expense_entries: Collection
     * }
     */
    public function getProjectProfitAndLoss(Project $project): array
    {
        $breakdown = $this->getAssignmentCostBreakdown($project);
        $invoiced = $this->financeService->sumProjectInvoicedAmount($project);
        $expenses = $this->sumProjectExpenses($project);
        $laborCost = $breakdown['labor_cost'];
        $totalCost = round($laborCost + $expenses, 2);
        $profitOrLoss = round($invoiced - $totalCost, 2);
        $currency = $project->currency
            ?? $project->deal?->currency
            ?? app(CurrencyService::class)->defaultCurrency();

        return [
            'currency' => $currency,
            'working_days_per_month' => $this->workingDaysPerMonth(),
            'invoiced' => $invoiced,
            'labor_cost' => $laborCost,
            'expenses' => $expenses,
            'total_cost' => $totalCost,
            'profit_or_loss' => $profitOrLoss,
            'is_profit' => $profitOrLoss > 0,
            'is_loss' => $profitOrLoss < 0,
            'margin_rate' => $invoiced > 0
                ? round(($profitOrLoss / $invoiced) * 100, 1)
                : 0.0,
            'assignments' => $breakdown['assignments'],
            'expense_entries' => $this->getProjectExpenseEntries($project),
        ];
    }
}

<?php

namespace Modules\CRM\Services\SalesTarget;

use Illuminate\Support\Collection;
use Modules\CRM\Models\CrmSalesTarget;
use Modules\User\Models\Employee;

class SalesTargetService
{
    public function listForReps(): Collection
    {
        $defaultDeals = (int) config('crm.sales_target_per_period', 10);

        return Employee::query()
            ->assignable()
            ->with('crmSalesTarget:id,employee_id,deals_target,value_target')
            ->select(['id', 'name', 'email'])
            ->get()
            ->map(function (Employee $employee) use ($defaultDeals) {
                $target = $employee->crmSalesTarget;

                return [
                    'employee_id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'deals_target' => $target?->deals_target ?? $defaultDeals,
                    'value_target' => $target?->value_target,
                ];
            });
    }

    public function dealsTargetForEmployee(int $employeeId): int
    {
        $target = CrmSalesTarget::query()->where('employee_id', $employeeId)->value('deals_target');

        return $target ?? (int) config('crm.sales_target_per_period', 10);
    }

    public function targetsForEmployees(array $employeeIds): array
    {
        if ($employeeIds === []) {
            return [];
        }

        $default = (int) config('crm.sales_target_per_period', 10);
        $rows = CrmSalesTarget::query()
            ->whereIn('employee_id', $employeeIds)
            ->pluck('deals_target', 'employee_id');

        return collect($employeeIds)->mapWithKeys(fn (int $id) => [
            $id => (int) ($rows[$id] ?? $default),
        ])->all();
    }

    public function sync(array $targets): void
    {
        foreach ($targets as $row) {
            $employeeId = (int) ($row['employee_id'] ?? 0);
            $dealsTarget = max(1, (int) ($row['deals_target'] ?? config('crm.sales_target_per_period', 10)));
            $valueTarget = isset($row['value_target']) && $row['value_target'] !== ''
                ? (float) $row['value_target']
                : null;

            if ($employeeId <= 0) {
                continue;
            }

            CrmSalesTarget::query()->updateOrCreate(
                ['employee_id' => $employeeId],
                [
                    'deals_target' => $dealsTarget,
                    'value_target' => $valueTarget,
                ],
            );
        }
    }
}

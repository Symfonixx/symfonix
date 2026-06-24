<?php

namespace Modules\CRM\Services\SalesTarget;

use App\Models\User;
use Illuminate\Support\Collection;
use Modules\CRM\Models\CrmSalesTarget;

class SalesTargetService
{
    public function listForReps(): Collection
    {
        $defaultDeals = (int) config('crm.sales_target_per_period', 10);

        return User::query()
            ->whereIn('type', [User::TYPE_EMPLOYEE, User::TYPE_ADMIN])
            ->select(['id', 'name', 'email', 'type'])
            ->orderBy('name')
            ->get()
            ->map(function (User $user) use ($defaultDeals) {
                $target = $user->crmSalesTarget;

                return [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type' => $user->type,
                    'deals_target' => $target?->deals_target ?? $defaultDeals,
                    'value_target' => $target?->value_target,
                ];
            });
    }

    public function dealsTargetForUser(int $userId): int
    {
        $target = CrmSalesTarget::query()->where('user_id', $userId)->value('deals_target');

        return $target ?? (int) config('crm.sales_target_per_period', 10);
    }

    public function targetsForUsers(array $userIds): array
    {
        if ($userIds === []) {
            return [];
        }

        $default = (int) config('crm.sales_target_per_period', 10);
        $rows = CrmSalesTarget::query()
            ->whereIn('user_id', $userIds)
            ->pluck('deals_target', 'user_id');

        return collect($userIds)->mapWithKeys(fn (int $id) => [
            $id => (int) ($rows[$id] ?? $default),
        ])->all();
    }

    public function sync(array $targets): void
    {
        foreach ($targets as $row) {
            $userId = (int) ($row['user_id'] ?? 0);
            $dealsTarget = max(1, (int) ($row['deals_target'] ?? config('crm.sales_target_per_period', 10)));
            $valueTarget = isset($row['value_target']) && $row['value_target'] !== ''
                ? (float) $row['value_target']
                : null;

            if ($userId <= 0) {
                continue;
            }

            CrmSalesTarget::query()->updateOrCreate(
                ['user_id' => $userId],
                [
                    'deals_target' => $dealsTarget,
                    'value_target' => $valueTarget,
                ],
            );
        }
    }
}

<?php

namespace Modules\CRM\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Modules\User\Support\EmployeeAccess;

class CrmAccess
{
    public static function canViewAllDeals(User $user): bool
    {
        return $user->can('sales.deals.view_all');
    }

    public static function canAccessDeal(User $user, ?int $assignedToEmployeeId, string $permission = 'sales.deals.view'): bool
    {
        if (! $user->can($permission)) {
            return false;
        }

        if (self::canViewAllDeals($user)) {
            return true;
        }

        $employeeId = EmployeeAccess::idForUser($user);

        return $assignedToEmployeeId === null
            || ($employeeId !== null && $assignedToEmployeeId === $employeeId);
    }

    public static function scopeDealsForUser(Builder $query, ?User $user = null): Builder
    {
        $user ??= auth()->user();

        if (! $user || self::canViewAllDeals($user)) {
            return $query;
        }

        $employeeId = EmployeeAccess::idForUser($user);

        if ($employeeId === null) {
            return $query->whereNull('assigned_to');
        }

        return $query->where(function (Builder $builder) use ($employeeId) {
            $builder
                ->where('assigned_to', $employeeId)
                ->orWhereNull('assigned_to');
        });
    }
}

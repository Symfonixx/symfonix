<?php

namespace Modules\CRM\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CrmAccess
{
    public static function canViewAllDeals(User $user): bool
    {
        return $user->type === User::TYPE_ADMIN || $user->can('CRM View All');
    }

    public static function canAccessDeal(User $user, ?int $assignedTo): bool
    {
        if (! $user->can('CRM Management')) {
            return false;
        }

        if (self::canViewAllDeals($user)) {
            return true;
        }

        return $assignedTo === null || $assignedTo === $user->id;
    }

    public static function scopeDealsForUser(Builder $query, ?User $user = null): Builder
    {
        $user ??= auth()->user();

        if (! $user || self::canViewAllDeals($user)) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($user) {
            $builder
                ->where('assigned_to', $user->id)
                ->orWhereNull('assigned_to');
        });
    }
}

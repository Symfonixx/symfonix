<?php

namespace Modules\CRM\Filters\Subscription;

use Illuminate\Database\Eloquent\Builder;

class SubscriptionFilter
{
    public function apply(Builder $query, array $filters = []): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhereHas('company', fn (Builder $q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['billing_cycle'])) {
            $query->where('billing_cycle', $filters['billing_cycle']);
        }

        if (! empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (filter_var($filters['renewing_soon'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $query->renewingSoon((int) ($filters['renewing_days'] ?? 30));
        }

        if (filter_var($filters['with_trashed'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $query->withTrashed();
        }

        return $query;
    }
}

<?php

namespace Modules\CRM\Filters\Company;

use Illuminate\Database\Eloquent\Builder;

class CompanyFilter
{
    public function apply(Builder $query, array $filters = []): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['country'])) {
            $query->where('country', 'like', '%'.$filters['country'].'%');
        }

        if (! empty($filters['city'])) {
            $query->where('city', 'like', '%'.$filters['city'].'%');
        }

        if (! empty($filters['activity_type'])) {
            $query->where('activity_type', $filters['activity_type']);
        }

        if (filter_var($filters['with_trashed'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $query->withTrashed();
        }

        return $query;
    }
}

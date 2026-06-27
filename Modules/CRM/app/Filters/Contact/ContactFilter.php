<?php

namespace Modules\CRM\Filters\Contact;

use Illuminate\Database\Eloquent\Builder;

class ContactFilter
{
    public function apply(Builder $query, array $filters = []): Builder
    {
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['company_id'])) {
            $query->where('company_id', (int) $filters['company_id']);
        }

        return $query;
    }
}

<?php

namespace Modules\CRM\Filters\Lead;

use Illuminate\Database\Eloquent\Builder;

class LeadFilter
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
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        if (! empty($filters['company_id'])) {
            $query->where('company_id', (int) $filters['company_id']);
        }

        if (! empty($filters['assigned_to'])) {
            $query->where('assigned_to', (int) $filters['assigned_to']);
        }

        if (! empty($filters['tag_id'])) {
            $tagId = (int) $filters['tag_id'];
            $query->whereHas('tags', fn (Builder $builder) => $builder->where('lead_tags.id', $tagId));
        }

        return $query;
    }
}

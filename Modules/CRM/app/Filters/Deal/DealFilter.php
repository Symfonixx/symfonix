<?php

namespace Modules\CRM\Filters\Deal;

use Illuminate\Database\Eloquent\Builder;

class DealFilter
{
    public function apply(Builder $query, array $filters = []): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('source', 'like', "%{$search}%")
                    ->orWhereHas('company', fn (Builder $q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['pipeline_stage_id'])) {
            $query->where('pipeline_stage_id', $filters['pipeline_stage_id']);
        }

        if (! empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (! empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (! empty($filters['tag_id'])) {
            $tagId = (int) $filters['tag_id'];
            $query->whereHas('lead.tags', fn (Builder $builder) => $builder->where('lead_tags.id', $tagId));
        }

        if (filter_var($filters['with_trashed'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $query->withTrashed();
        }

        return $query;
    }
}

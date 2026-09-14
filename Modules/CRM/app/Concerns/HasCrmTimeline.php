<?php

namespace Modules\CRM\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Models\CrmAuditLog;

trait HasCrmTimeline
{
    public function crmActivities(): MorphMany
    {
        return $this->morphMany(CrmActivity::class, 'subject')->latest();
    }

    public function crmAuditLogs(): MorphMany
    {
        return $this->morphMany(CrmAuditLog::class, 'subject')->latest('created_at');
    }

    public function crmTimeline(int $limit = 50): Collection
    {
        $activities = $this->relationLoaded('crmActivities')
            ? $this->crmActivities->take($limit)
            : $this->crmActivities()->with('user:id,name')->limit($limit)->get();

        $audits = $this->relationLoaded('crmAuditLogs')
            ? $this->crmAuditLogs->take($limit)
            : $this->crmAuditLogs()->with('user:id,name')->limit($limit)->get();

        return $activities->map(fn (CrmActivity $item) => [
            'kind' => 'activity',
            'occurred_at' => $item->created_at,
            'item' => $item,
        ])
            ->toBase()
            ->merge($audits->map(fn (CrmAuditLog $item) => [
                'kind' => 'audit',
                'occurred_at' => $item->created_at,
                'item' => $item,
            ]))
            ->sortByDesc(fn (array $entry) => $entry['occurred_at'])
            ->take($limit)
            ->values();
    }
}

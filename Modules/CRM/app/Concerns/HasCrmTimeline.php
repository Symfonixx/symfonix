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
            ? $this->crmActivities
            : $this->crmActivities()->with('user:id,name')->limit($limit)->get();

        $audits = $this->relationLoaded('crmAuditLogs')
            ? $this->crmAuditLogs
            : $this->crmAuditLogs()->with('user:id,name')->limit($limit)->get();

        // #region agent log
        $mappedActivities = $activities->map(fn (CrmActivity $item) => [
            'kind' => 'activity',
            'occurred_at' => $item->created_at,
            'item' => $item,
        ]);
        file_put_contents(base_path('debug-245f51.log'), json_encode(['sessionId' => '245f51', 'hypothesisId' => 'A', 'location' => 'HasCrmTimeline.php:crmTimeline', 'message' => 'mapped activities collection class', 'data' => ['class' => $mappedActivities::class, 'firstItemType' => $mappedActivities->isNotEmpty() ? gettype($mappedActivities->first()) : 'empty'], 'timestamp' => (int) (microtime(true) * 1000)]).PHP_EOL, FILE_APPEND);
        // #endregion

        return $mappedActivities
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

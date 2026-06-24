<?php

namespace Modules\CRM\Services\Analytics;

use Modules\CRM\Models\Lead;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Models\CrmAuditLog;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\PipelineStage;
use Modules\CRM\Support\CrmAccess;
use Modules\CRM\Support\DateRangeResolver;

class CrmAnalyticsService
{
    public function build(array $filters = []): array
    {
        $period = $filters['period'] ?? 'this_month';
        $assigneeId = ! empty($filters['assigned_to']) ? (int) $filters['assigned_to'] : null;
        $range = DateRangeResolver::resolve($period, $filters['date_from'] ?? null, $filters['date_to'] ?? null);

        $summary = $this->summaryMetrics($range, $assigneeId);
        $pipelineFunnel = $this->pipelineFunnel($assigneeId);
        $leadChannels = $this->leadChannels($range, $assigneeId);
        $teamLeaderboard = $this->teamLeaderboard($range, $assigneeId);
        $recentActivity = $this->recentActivity($assigneeId);

        return [
            'filters' => [
                'period' => $period,
                'assigned_to' => $assigneeId,
                'date_from' => $range['start']->toDateString(),
                'date_to' => $range['end']->toDateString(),
                'label' => $range['label'],
            ],
            'summary' => $summary,
            'pipeline_funnel' => $pipelineFunnel,
            'lead_channels' => $leadChannels,
            'team_leaderboard' => $teamLeaderboard,
            'recent_activity' => $recentActivity,
            'assignees' => $this->assignees(),
            'chart_colors' => [
                '#3E97FF', '#50CD89', '#FFC700', '#7239EA', '#F1416C', '#181C32', '#A1A5B7',
            ],
        ];
    }

    private function summaryMetrics(array $range, ?int $assigneeId): array
    {
        $currentLeads = $this->leadsQuery($assigneeId)
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->count();

        $previousLeads = $this->leadsQuery($assigneeId)
            ->whereBetween('created_at', [$range['previous_start'], $range['previous_end']])
            ->count();

        $totalLeadsAll = $this->leadsQuery($assigneeId)->count();
        $convertedToWon = $this->leadsQuery($assigneeId)
            ->whereNotNull('deal_id')
            ->whereHas('deal', fn ($q) => $q->where('status', Deal::STATUS_WON))
            ->count();

        $conversionRate = $totalLeadsAll > 0
            ? round(($convertedToWon / $totalLeadsAll) * 100, 1)
            : 0;

        $previousConverted = $this->leadsQuery($assigneeId)
            ->whereNotNull('deal_id')
            ->whereHas('deal', fn ($q) => $q
                ->where('status', Deal::STATUS_WON)
                ->whereBetween('won_at', [$range['previous_start'], $range['previous_end']]))
            ->count();

        $currentConverted = $this->leadsQuery($assigneeId)
            ->whereNotNull('deal_id')
            ->whereHas('deal', fn ($q) => $q
                ->where('status', Deal::STATUS_WON)
                ->whereBetween('won_at', [$range['start'], $range['end']]))
            ->count();

        $pipelineValue = (float) $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_OPEN)
            ->sum('value');

        $previousPipelineValue = (float) $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_OPEN)
            ->where('created_at', '<=', $range['previous_end'])
            ->sum('value');

        $wonDealsQuery = $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_WON)
            ->whereBetween('won_at', [$range['start'], $range['end']]);

        $wonDealsCount = (clone $wonDealsQuery)->count();
        $wonDealsValue = (float) (clone $wonDealsQuery)->sum('value');

        $previousWonCount = $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_WON)
            ->whereBetween('won_at', [$range['previous_start'], $range['previous_end']])
            ->count();

        return [
            'total_leads' => [
                'value' => $currentLeads,
                'trend' => $this->trend($currentLeads, $previousLeads),
            ],
            'conversion_rate' => [
                'value' => $conversionRate,
                'trend' => $this->trend($currentConverted, max(1, $previousConverted)),
                'converted' => $convertedToWon,
                'total' => $totalLeadsAll,
            ],
            'pipeline_value' => [
                'value' => $pipelineValue,
                'trend' => $this->trend($pipelineValue, $previousPipelineValue),
                'currency' => config('crm.default_currency', 'USD'),
            ],
            'won_deals' => [
                'count' => $wonDealsCount,
                'value' => $wonDealsValue,
                'trend' => $this->trend($wonDealsCount, $previousWonCount),
                'currency' => config('crm.default_currency', 'USD'),
            ],
        ];
    }

    private function pipelineFunnel(?int $assigneeId): array
    {
        $stages = PipelineStage::query()->active()->ordered()->get();
        $maxCount = 1;

        $unconvertedLeads = $this->leadsQuery($assigneeId)->whereNull('deal_id')->count();
        $maxCount = max($maxCount, $unconvertedLeads);

        $stageStats = $stages->map(function (PipelineStage $stage) use ($assigneeId, &$maxCount) {
            $query = $this->dealsQuery($assigneeId)->where('pipeline_stage_id', $stage->id);
            $count = (clone $query)->count();
            $value = (float) (clone $query)->sum('value');
            $maxCount = max($maxCount, $count);

            return [
                'id' => $stage->id,
                'name' => $stage->name,
                'color' => $stage->color,
                'count' => $count,
                'value' => $value,
                'is_won' => $stage->is_won,
                'is_lost' => $stage->is_lost,
            ];
        });

        $funnel = collect([
            [
                'id' => 'leads',
                'name' => __('crm::dashboard.funnel.unconverted_leads'),
                'color' => 'info',
                'count' => $unconvertedLeads,
                'value' => 0,
                'is_won' => false,
                'is_lost' => false,
            ],
        ])->merge($stageStats);

        return $funnel->map(function (array $row) use ($maxCount) {
            $row['percentage'] = $maxCount > 0 ? round(($row['count'] / $maxCount) * 100) : 0;

            return $row;
        })->values()->all();
    }

    private function leadChannels(array $range, ?int $assigneeId): array
    {
        $rows = $this->leadsQuery($assigneeId)
            ->select('source', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->groupBy('source')
            ->orderByDesc('total')
            ->get();

        $total = max(1, $rows->sum('total'));

        $channelMap = collect(Lead::SOURCES)->mapWithKeys(fn (string $source) => [
            $source => __('crm::dashboard.channels.'.$source),
        ])->all();

        return $rows->map(fn ($row) => [
            'key' => $row->source ?: 'unknown',
            'label' => $channelMap[$row->source] ?? __('crm::dashboard.channels.unknown'),
            'count' => (int) $row->total,
            'percentage' => round(((int) $row->total / $total) * 100, 1),
        ])->values()->all();
    }

    private function teamLeaderboard(array $range, ?int $assigneeId): array
    {
        $rows = $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_WON)
            ->whereBetween('won_at', [$range['start'], $range['end']])
            ->whereNotNull('assigned_to')
            ->selectRaw('assigned_to, COUNT(*) as deals_count, COALESCE(SUM(value), 0) as total_value')
            ->groupBy('assigned_to')
            ->orderByDesc('deals_count')
            ->limit(10)
            ->get();

        $userIds = $rows->pluck('assigned_to')->map(fn ($id) => (int) $id)->all();
        $targets = app(\Modules\CRM\Services\SalesTarget\SalesTargetService::class)->targetsForUsers($userIds);

        $users = User::query()
            ->whereIn('id', $userIds)
            ->pluck('name', 'id');

        return $rows->map(function ($row) use ($users, $targets) {
            $count = (int) $row->deals_count;
            $target = $targets[(int) $row->assigned_to] ?? (int) config('crm.sales_target_per_period', 10);
            $achievement = $target > 0 ? min(100, round(($count / $target) * 100)) : 0;

            return [
                'user_id' => (int) $row->assigned_to,
                'name' => $users[$row->assigned_to] ?? __('crm::dashboard.unknown_rep'),
                'closed_deals' => $count,
                'closed_value' => (float) $row->total_value,
                'target' => $target,
                'achievement' => $achievement,
            ];
        })->values()->all();
    }

    private function recentActivity(?int $assigneeId): array
    {
        $dealIds = $assigneeId
            ? $this->dealsQuery($assigneeId)->pluck('id')
            : null;

        $auditQuery = CrmAuditLog::query()
            ->with('user:id,name')
            ->latest('created_at')
            ->limit(20);

        if ($assigneeId && $dealIds) {
            $auditQuery->where(function ($q) use ($dealIds, $assigneeId) {
                $q->where(function ($inner) use ($dealIds) {
                    $inner->where('subject_type', Deal::class)->whereIn('subject_id', $dealIds);
                })->orWhere('user_id', $assigneeId);
            });
        }

        $activityQuery = CrmActivity::query()
            ->with('user:id,name')
            ->latest()
            ->limit(20);

        if ($assigneeId) {
            $activityQuery->where('user_id', $assigneeId);
        }

        $audits = $auditQuery->get()->map(fn (CrmAuditLog $log) => [
            'kind' => 'audit',
            'message' => $log->description ?? __('crm::dashboard.activity.fallback'),
            'user' => $log->user?->name ?? __('crm::timeline.system'),
            'occurred_at' => $log->created_at,
            'event' => $log->event,
            'icon' => $this->eventIcon($log->event),
            'color' => $this->eventColor($log->event),
        ]);

        $activities = $activityQuery->get()->map(fn (CrmActivity $activity) => [
            'kind' => 'activity',
            'message' => __('crm::dashboard.activity.logged', [
                'type' => __('crm::timeline.activity_types.'.$activity->type),
                'title' => $activity->title ?: Str::limit($activity->body, 60),
            ]),
            'user' => $activity->user?->name ?? __('crm::timeline.system'),
            'occurred_at' => $activity->created_at,
            'event' => $activity->type,
            'icon' => $this->activityIcon($activity->type),
            'color' => 'primary',
        ]);

        return $audits->merge($activities)
            ->sortByDesc('occurred_at')
            ->take(15)
            ->values()
            ->all();
    }

    private function assignees(): Collection
    {
        return User::query()
            ->whereIn('type', [User::TYPE_EMPLOYEE, User::TYPE_ADMIN])
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }

    private function leadsQuery(?int $assigneeId)
    {
        $query = Lead::query();

        if ($assigneeId) {
            $query->where('assigned_to', $assigneeId);
        }

        return $query;
    }

    private function dealsQuery(?int $assigneeId)
    {
        $query = Deal::query()->visibleTo();

        if ($assigneeId) {
            $query->where('assigned_to', $assigneeId);
        }

        return $query;
    }

    private function trend(float|int $current, float|int $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function eventIcon(string $event): string
    {
        return match ($event) {
            'created' => 'plus-circle',
            'deleted', 'activity_removed' => 'trash',
            'stage_changed' => 'arrow-right-circle',
            'converted' => 'arrow-repeat',
            'updated' => 'pencil-square',
            default => 'clock-history',
        };
    }

    private function eventColor(string $event): string
    {
        return match ($event) {
            'created', 'converted' => 'success',
            'deleted', 'activity_removed' => 'danger',
            'stage_changed' => 'info',
            default => 'secondary',
        };
    }

    private function activityIcon(string $type): string
    {
        return match ($type) {
            'call' => 'telephone',
            'meeting' => 'people',
            'task' => 'check2-square',
            'email' => 'envelope',
            default => 'journal-text',
        };
    }
}

<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Modules\AI\Support\ToolResult;
use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Models\Lead;

class GetLeadStatsTool extends AbstractAssistantTool
{
    public function name(): string
    {
        return 'get_lead_stats';
    }

    public function description(): string
    {
        return 'Get lead counts and which leads need follow-up today. Use this for "which leads should we follow up today".';
    }

    public function parameters(): array
    {
        return $this->periodParameters();
    }

    public function permissions(): array
    {
        return ['crm.leads.view'];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $range = $this->period($arguments);
        $inProgress = [Lead::STATUS_NEW, Lead::STATUS_CONTACTED, Lead::STATUS_QUALIFIED];

        $byStatus = Lead::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $limit = $this->limiter->maxListItems();
        $dueTodayIds = collect();
        if ($user->can('crm.activities.view')) {
            $dueTodayIds = CrmActivity::query()
                ->where('type', CrmActivity::TYPE_TASK)
                ->whereNull('completed_at')
                ->where('subject_type', Lead::class)
                ->whereDate('scheduled_at', now()->toDateString())
                ->pluck('subject_id');
        }

        $dueToday = Lead::query()
            ->with(['assignee:id,name'])
            ->where(function ($query) use ($dueTodayIds, $inProgress) {
                $query->whereIn('id', $dueTodayIds)
                    ->orWhere(function ($inner) use ($inProgress) {
                        $inner->whereIn('status', $inProgress)
                            ->whereDate('created_at', now()->toDateString());
                    });
            })
            ->orderBy('updated_at')
            ->limit($limit)
            ->get(['id', 'name', 'email', 'status', 'assigned_to', 'updated_at', 'created_at'])
            ->unique('id')
            ->values();

        $followUps = Lead::query()
            ->with(['assignee:id,name'])
            ->whereIn('status', $inProgress)
            ->where('updated_at', '<=', now()->subDays(3))
            ->orderBy('updated_at')
            ->limit($limit)
            ->get(['id', 'name', 'email', 'status', 'assigned_to', 'updated_at', 'created_at']);

        $mapLead = fn (Lead $lead) => [
            'id' => $lead->id,
            'name' => $lead->name,
            'email' => $lead->email,
            'status' => $lead->status,
            'assignee' => $lead->assignee?->name,
            'updated_at' => $lead->updated_at?->toDateString(),
        ];

        $data = [
            'period' => $range['period'],
            'period_label' => $range['source_label'],
            'total' => Lead::query()->count(),
            'new_in_period' => Lead::query()->whereBetween('created_at', [$range['start'], $range['end']])->count(),
            'in_progress' => Lead::query()->whereIn('status', $inProgress)->count(),
            'converted' => Lead::query()->where('status', Lead::STATUS_CONVERTED)->count(),
            'by_status' => $byStatus,
            'follow_up_today' => $dueToday->map($mapLead)->all(),
            'needs_follow_up' => $followUps->map($mapLead)->all(),
        ];

        return ToolResult::success($data, ['Leads', $range['source_label']]);
    }
}

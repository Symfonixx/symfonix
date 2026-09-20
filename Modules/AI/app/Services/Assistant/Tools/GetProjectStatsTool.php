<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Modules\AI\Support\ToolResult;
use Modules\Project\Models\Project;

class GetProjectStatsTool extends AbstractAssistantTool
{
    public function name(): string
    {
        return 'get_project_stats';
    }

    public function description(): string
    {
        return 'Get project totals and which projects are overdue. Use this for overdue project questions.';
    }

    public function parameters(): array
    {
        return $this->periodParameters();
    }

    public function permissions(): array
    {
        return ['project.projects.view'];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $range = $this->period($arguments);

        $active = Project::query()
            ->whereHas('status', fn ($q) => $q->where('name', '!=', 'Completed'))
            ->count();

        $overdueQuery = Project::query()
            ->with(['status:id,name', 'company:id,name'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereHas('status', fn ($q) => $q->where('name', '!=', 'Completed'));

        $overdue = (clone $overdueQuery)
            ->orderBy('due_date')
            ->limit($this->limiter->maxListItems())
            ->get(['id', 'title', 'due_date', 'company_id', 'project_status_id', 'payment_status']);

        $data = [
            'period' => $range['period'],
            'period_label' => $range['source_label'],
            'total' => Project::query()->count(),
            'active' => $active,
            'created_in_period' => Project::query()->whereBetween('created_at', [$range['start'], $range['end']])->count(),
            'unpaid' => Project::query()->where('payment_status', Project::PAYMENT_UNPAID)->count(),
            'overdue_count' => (clone $overdueQuery)->count(),
            'overdue' => $overdue->map(fn (Project $project) => [
                'id' => $project->id,
                'title' => $project->title,
                'company' => $project->company?->name,
                'status' => $project->status?->name,
                'due_date' => $project->due_date?->toDateString(),
                'payment_status' => $project->payment_status,
            ])->all(),
        ];

        return ToolResult::success($data, ['Projects', $range['source_label']]);
    }
}

<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Modules\AI\Support\ToolResult;
use Modules\Project\Models\Project;

class GetProjectDetailsTool extends AbstractAssistantTool
{
    public function name(): string
    {
        return 'get_project_details';
    }

    public function description(): string
    {
        return 'Get details for one project: status, dates, company, team, payment status.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'project_id' => [
                    'type' => 'integer',
                    'description' => 'Project id',
                ],
            ],
            'required' => ['project_id'],
        ];
    }

    public function permissions(): array
    {
        return ['project.projects.view'];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $id = (int) ($arguments['project_id'] ?? 0);
        $project = Project::query()
            ->with([
                'status:id,name',
                'company:id,name',
                'employees:id,name',
            ])
            ->find($id);

        if ($project === null) {
            return ToolResult::empty(__('ai::assistant.errors.not_found'), ['Projects']);
        }

        $overdue = $project->due_date !== null
            && $project->due_date->isPast()
            && ! $project->isCompleted();

        $data = $this->limiter->truncate([
            'id' => $project->id,
            'title' => $project->title,
            'description' => $project->description,
            'status' => $project->status?->name,
            'company' => $project->company?->name,
            'start_date' => $project->start_date?->toDateString(),
            'due_date' => $project->due_date?->toDateString(),
            'overdue' => $overdue,
            'payment_status' => $project->payment_status,
            'budget' => $project->budget,
            'currency' => $project->currency,
            'team' => $project->employees->pluck('name')->take($this->limiter->maxListItems())->values()->all(),
        ]);

        return ToolResult::success($data, ['Projects']);
    }
}

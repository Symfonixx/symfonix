<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\AI\Support\ToolResult;
use Modules\CRM\Models\CrmActivity;
use Modules\Project\Models\Project;
use Modules\Project\Models\ProjectEmployee;
use Modules\User\Models\Employee;

class GetOverdueWorkTool extends AbstractAssistantTool
{
    public function name(): string
    {
        return 'get_overdue_work';
    }

    public function description(): string
    {
        return 'Get overdue projects, overdue CRM tasks, and who owns the most overdue work. Use this for "who has the most overdue tasks" and overdue project lists. There is no separate task module.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => new \stdClass,
        ];
    }

    public function permissions(): array
    {
        return ['project.projects.view', 'crm.activities.view'];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $limit = $this->limiter->maxListItems();
        $sources = [];
        $projects = [];
        $tasks = [];
        $overdueProjectCount = 0;
        $overdueTaskCount = 0;
        $peopleWithMostOverdueTasks = [];
        $peopleWithMostOverdueProjects = [];

        if ($user->can('project.projects.view')) {
            $sources[] = 'Projects';
            $overdueProjects = Project::query()
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', now()->toDateString())
                ->whereHas('status', fn ($query) => $query->where('name', '!=', 'Completed'));

            $overdueProjectCount = (clone $overdueProjects)->count();
            $projects = (clone $overdueProjects)
                ->with(['status:id,name', 'company:id,name'])
                ->orderBy('due_date')
                ->limit($limit)
                ->get(['id', 'title', 'due_date', 'company_id', 'project_status_id'])
                ->map(fn (Project $project) => [
                    'id' => $project->id,
                    'title' => $project->title,
                    'company' => $project->company?->name,
                    'due_date' => $project->due_date?->toDateString(),
                    'status' => $project->status?->name,
                ])->all();

            $projectIds = (clone $overdueProjects)->pluck('id');
            if ($projectIds->isNotEmpty()) {
                $rows = ProjectEmployee::query()
                    ->active()
                    ->whereIn('project_id', $projectIds)
                    ->selectRaw('employee_id, COUNT(*) as overdue_count')
                    ->groupBy('employee_id')
                    ->orderByDesc('overdue_count')
                    ->limit($limit)
                    ->get();

                $employees = Employee::query()
                    ->whereIn('id', $rows->pluck('employee_id')->filter())
                    ->get(['id', 'name'])
                    ->keyBy('id');

                $peopleWithMostOverdueProjects = $rows->map(fn ($row) => [
                    'employee_id' => $row->employee_id,
                    'name' => $employees->get($row->employee_id)?->name ?: __('ai::assistant.unassigned'),
                    'overdue_projects' => (int) $row->overdue_count,
                ])->all();
            }
        }

        if ($user->can('crm.activities.view')) {
            $sources[] = 'CRM tasks';
            $overdueTasks = CrmActivity::query()
                ->where('type', CrmActivity::TYPE_TASK)
                ->whereNull('completed_at')
                ->whereNotNull('scheduled_at')
                ->where('scheduled_at', '<', now());

            $overdueTaskCount = (clone $overdueTasks)->count();
            $tasks = (clone $overdueTasks)
                ->with('user:id,name')
                ->orderBy('scheduled_at')
                ->limit($limit)
                ->get(['id', 'title', 'scheduled_at', 'user_id', 'subject_type', 'subject_id'])
                ->map(fn (CrmActivity $activity) => [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'owner' => $activity->user?->name ?: __('ai::assistant.unassigned'),
                    'scheduled_at' => $activity->scheduled_at?->toDateTimeString(),
                ])->all();

            $rows = (clone $overdueTasks)
                ->select('user_id', DB::raw('COUNT(*) as overdue_count'))
                ->groupBy('user_id')
                ->orderByDesc('overdue_count')
                ->limit($limit)
                ->get();

            $owners = User::query()
                ->whereIn('id', $rows->pluck('user_id')->filter())
                ->get(['id', 'name'])
                ->keyBy('id');

            $peopleWithMostOverdueTasks = $rows->map(fn ($row) => [
                'user_id' => $row->user_id,
                'name' => $owners->get($row->user_id)?->name ?: __('ai::assistant.unassigned'),
                'overdue_tasks' => (int) $row->overdue_count,
            ])->all();
        }

        if ($sources === []) {
            return ToolResult::denied($this->deniedMessage());
        }

        return ToolResult::success([
            'note' => 'Overdue work maps to overdue projects and overdue CRM tasks. There is no standalone task module.',
            'overdue_project_count' => $overdueProjectCount,
            'overdue_crm_task_count' => $overdueTaskCount,
            'people_with_most_overdue_tasks' => $peopleWithMostOverdueTasks,
            'people_with_most_overdue_projects' => $peopleWithMostOverdueProjects,
            'overdue_projects' => $projects,
            'overdue_crm_tasks' => $tasks,
        ], $sources);
    }
}

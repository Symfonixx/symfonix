<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Modules\AI\Support\ToolResult;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Lead;
use Modules\Project\Models\Project;

class SearchRecordsTool extends AbstractAssistantTool
{
    public function name(): string
    {
        return 'search_records';
    }

    public function description(): string
    {
        return 'Search leads, customers (companies), or projects by name or email. Use this before details tools when you do not have an id.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'type' => [
                    'type' => 'string',
                    'enum' => ['lead', 'customer', 'project'],
                    'description' => 'Record type to search',
                ],
                'query' => [
                    'type' => 'string',
                    'description' => 'Name, email, or title fragment',
                ],
            ],
            'required' => ['type', 'query'],
        ];
    }

    public function permissions(): array
    {
        return ['crm.leads.view', 'crm.companies.view', 'project.projects.view'];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $type = (string) ($arguments['type'] ?? '');
        $query = trim((string) ($arguments['query'] ?? ''));
        $limit = min(8, $this->limiter->maxListItems());

        if ($query === '') {
            return ToolResult::empty(__('ai::assistant.errors.not_found'));
        }

        $like = '%'.$query.'%';

        if ($type === 'lead') {
            if (! $user->can('crm.leads.view')) {
                return ToolResult::denied($this->deniedMessage());
            }

            $rows = Lead::query()
                ->where(function ($builder) use ($like) {
                    $builder->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('company_name', 'like', $like);
                })
                ->limit($limit)
                ->get(['id', 'name', 'email', 'status']);

            return ToolResult::success([
                'type' => 'lead',
                'results' => $rows->map(fn (Lead $lead) => [
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'status' => $lead->status,
                ])->all(),
            ], ['Leads']);
        }

        if ($type === 'customer') {
            if (! $user->can('crm.companies.view')) {
                return ToolResult::denied($this->deniedMessage());
            }

            $rows = Company::query()
                ->where(function ($builder) use ($like) {
                    $builder->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like);
                })
                ->limit($limit)
                ->get(['id', 'name', 'email', 'status']);

            return ToolResult::success([
                'type' => 'customer',
                'results' => $rows->map(fn (Company $company) => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'email' => $company->email,
                    'status' => $company->status,
                ])->all(),
            ], ['Customers']);
        }

        if ($type === 'project') {
            if (! $user->can('project.projects.view')) {
                return ToolResult::denied($this->deniedMessage());
            }

            $rows = Project::query()
                ->with('status:id,name')
                ->where('title', 'like', $like)
                ->limit($limit)
                ->get(['id', 'title', 'project_status_id', 'due_date']);

            return ToolResult::success([
                'type' => 'project',
                'results' => $rows->map(fn (Project $project) => [
                    'id' => $project->id,
                    'title' => $project->title,
                    'status' => $project->status?->name,
                    'due_date' => $project->due_date?->toDateString(),
                ])->all(),
            ], ['Projects']);
        }

        return ToolResult::empty(__('ai::assistant.errors.not_found'));
    }
}

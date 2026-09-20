<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Modules\AI\Support\ToolResult;
use Modules\CRM\Models\Lead;

class GetLeadDetailsTool extends AbstractAssistantTool
{
    public function name(): string
    {
        return 'get_lead_details';
    }

    public function description(): string
    {
        return 'Get details for one lead by id. Use search_records first if you only have a name.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'lead_id' => [
                    'type' => 'integer',
                    'description' => 'Lead id',
                ],
            ],
            'required' => ['lead_id'],
        ];
    }

    public function permissions(): array
    {
        return ['crm.leads.view'];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $id = (int) ($arguments['lead_id'] ?? 0);
        $lead = Lead::query()
            ->with(['company:id,name', 'assignee:id,name'])
            ->find($id);

        if ($lead === null) {
            return ToolResult::empty(__('ai::assistant.errors.not_found'), ['Leads']);
        }

        $data = $this->limiter->truncate([
            'id' => $lead->id,
            'name' => $lead->name,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'status' => $lead->status,
            'source' => $lead->source,
            'company' => $lead->company?->name ?? $lead->company_name,
            'assignee' => $lead->assignee?->name,
            'job_title' => $lead->job_title,
            'problem_statement' => $lead->problem_statement,
            'created_at' => $lead->created_at?->toDateString(),
            'updated_at' => $lead->updated_at?->toDateString(),
        ]);

        return ToolResult::success($data, ['Leads']);
    }
}

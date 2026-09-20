<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Modules\AI\Support\CostLimiter;
use Modules\AI\Support\LeadFollowUpContext;
use Modules\AI\Support\ToolResult;
use Modules\CRM\Models\Lead;

class DraftFollowUpMessageTool extends AbstractAssistantTool
{
    public function __construct(
        CostLimiter $limiter,
        private readonly LeadFollowUpContext $context,
    ) {
        parent::__construct($limiter);
    }

    public function name(): string
    {
        return 'draft_follow_up_message';
    }

    public function description(): string
    {
        return 'Load lead, CRM activity, and related project context so you can draft a follow-up. This does not send email or WhatsApp.';
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
        $lead = Lead::query()->find($id);

        if ($lead === null) {
            return ToolResult::empty(__('ai::assistant.errors.not_found'), ['Leads']);
        }

        $data = $this->context->build($lead, $user);
        $data['instruction'] = 'Draft a follow-up message only. Do not send it. Do not claim it was sent. Use the lead data and project activity.';

        return ToolResult::success($this->limiter->truncate($data), ['Leads']);
    }
}

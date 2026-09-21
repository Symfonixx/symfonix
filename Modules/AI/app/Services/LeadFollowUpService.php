<?php

namespace Modules\AI\Services;

use App\Models\User;
use Modules\AI\Support\LeadFollowUpContext;
use Modules\AI\Support\LeadFollowUpSchema;
use Modules\CRM\Models\Lead;

class LeadFollowUpService
{
    public function __construct(
        private readonly ContentGenerationService $contentGenerationService,
        private readonly LeadFollowUpContext $context,
    ) {}

    /**
     * @return array{success: bool, fields: ?array{type: string, title: string, body: string, scheduled_at: string}, error: ?string, provider: ?string}
     */
    public function generate(Lead $lead, User $user, ?string $instruction = null, ?string $locale = null): array
    {
        $result = $this->contentGenerationService->generateJson(
            LeadFollowUpSchema::systemPrompt($locale ?: app()->getLocale()),
            LeadFollowUpSchema::userMessage($this->context->build($lead, $user), $instruction),
        );

        if (! $result['success']) {
            return [
                'success' => false,
                'fields' => null,
                'error' => $result['error'] ?? __('ai::content_generation.messages.request_failed'),
                'provider' => $result['provider'],
            ];
        }

        $fields = LeadFollowUpSchema::normalize($result['data'] ?? []);

        if ($fields['title'] === '' && $fields['body'] === '') {
            return [
                'success' => false,
                'fields' => null,
                'error' => __('ai::content_generation.messages.empty_result'),
                'provider' => $result['provider'],
            ];
        }

        if ($fields['title'] === '') {
            $fields['title'] = __('crm::lead.follow_up.default_title', [
                'name' => $lead->name ?: '#'.$lead->getKey(),
            ]);
        }

        return [
            'success' => true,
            'fields' => $fields,
            'error' => null,
            'provider' => $result['provider'],
        ];
    }
}

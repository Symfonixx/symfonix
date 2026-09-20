<?php

namespace Modules\AI\Services;

use Modules\AI\Support\FormContentSchema;
use Modules\AI\Support\MarketingEmailContentSchema;
use Modules\CRM\Models\MarketingGroup;

class MarketingEmailContentService
{
    public function __construct(
        private readonly ContentGenerationService $contentGenerationService,
    ) {}

    /**
     * @return array{success: bool, fields: ?array{subject: string, body: string}, error: ?string, provider: ?string}
     */
    public function generate(?MarketingGroup $group, ?string $title, ?string $goal, ?string $instruction = null, ?string $locale = null): array
    {
        $campaign = MarketingEmailContentSchema::campaignContext($group, $title, $goal);

        if ($campaign['title'] === '' || $campaign['goal'] === '') {
            return [
                'success' => false,
                'fields' => null,
                'error' => __('crm::marketing.ai.goal_required'),
                'provider' => null,
            ];
        }

        $result = $this->contentGenerationService->generateStructuredContent(
            MarketingEmailContentSchema::systemPrompt($locale ?: app()->getLocale()),
            MarketingEmailContentSchema::userMessage($campaign, $instruction),
        );

        if (! $result['success'] || ! is_string($result['content'])) {
            return [
                'success' => false,
                'fields' => null,
                'error' => $result['error'] ?? __('ai::content_generation.messages.request_failed'),
                'provider' => $result['provider'],
            ];
        }

        $decoded = FormContentSchema::decode($result['content']);

        if ($decoded === null) {
            return [
                'success' => false,
                'fields' => null,
                'error' => __('ai::content_generation.messages.empty_result'),
                'provider' => $result['provider'],
            ];
        }

        $fields = MarketingEmailContentSchema::normalize($decoded);

        if ($fields['subject'] === '' && trim(strip_tags($fields['body'])) === '') {
            return [
                'success' => false,
                'fields' => null,
                'error' => __('ai::content_generation.messages.empty_result'),
                'provider' => $result['provider'],
            ];
        }

        return [
            'success' => true,
            'fields' => $fields,
            'error' => null,
            'provider' => $result['provider'],
        ];
    }
}

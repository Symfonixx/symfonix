<?php

namespace Modules\AI\Services\Chatbot\Tools;

use Modules\AI\Contracts\PublicChatTool;
use Modules\AI\Support\PublicChatContext;
use Modules\AI\Support\ToolResult;

class SuggestQuickRepliesTool implements PublicChatTool
{
    public function name(): string
    {
        return 'suggest_quick_replies';
    }

    public function description(): string
    {
        return 'Show 2-4 short clickable follow-up buttons under your reply. Use for next steps such as a quote, a service area, or talking to the team.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'replies' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                        'description' => 'Short button label, also used as the follow-up message',
                    ],
                ],
            ],
            'required' => ['replies'],
        ];
    }

    public function handle(array $arguments, PublicChatContext $context): ToolResult
    {
        $replies = $arguments['replies'] ?? [];
        if (! is_array($replies)) {
            return ToolResult::empty('No quick replies were provided.');
        }

        $buttons = [];
        foreach ($replies as $reply) {
            if (is_string($reply)) {
                $label = trim($reply);
                $value = $label;
            } elseif (is_array($reply)) {
                $label = trim((string) ($reply['label'] ?? ''));
                $value = trim((string) ($reply['value'] ?? $label));
            } else {
                continue;
            }

            if ($label === '') {
                continue;
            }
            $buttons[] = [
                'label' => mb_substr($label, 0, 48),
                'value' => mb_substr($value !== '' ? $value : $label, 0, 180),
            ];

            if (count($buttons) >= 4) {
                break;
            }
        }

        $context->quickReplies = $buttons;

        return ToolResult::success([
            'count' => count($buttons),
            'replies' => $buttons,
        ]);
    }
}

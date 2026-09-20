<?php

namespace Modules\AI\Services\Chatbot;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Modules\AI\Services\AiProviderRouter;
use Modules\AI\Support\CostLimiter;
use Modules\AI\Support\PublicChatContext;
use Modules\AI\Support\PublicChatPrompt;

class PublicChatService
{
    public function __construct(
        private readonly AiProviderRouter $router,
        private readonly PublicChatToolRegistry $tools,
        private readonly PublicServiceCatalog $catalog,
        private readonly CostLimiter $limiter,
    ) {}

    public function isAvailable(): bool
    {
        return (bool) config('ai.chatbot.enabled', true) && $this->router->isConfigured();
    }

    /**
     * @param  list<array<string, mixed>>  $history
     * @return array{
     *     success: bool,
     *     reply: string,
     *     buttons: list<array{label: string, value: string}>,
     *     lead_id: ?int,
     *     provider: ?string,
     *     error: ?string
     * }
     */
    public function reply(string $userMessage, array $history, PublicChatContext $context): array
    {
        $userMessage = trim($userMessage);
        if ($userMessage === '') {
            return $this->failure($context, __('chat.ai.empty'));
        }

        $replyLocale = $this->replyLocale($userMessage, $context->locale);
        $previousLocale = app()->getLocale();
        app()->setLocale($replyLocale);

        try {
            if (! $this->isAvailable()) {
                return $this->catalogFallback($userMessage, $context, null);
            }

            $rateKey = 'ai-chatbot:'.($context->ipAddress ?: 'unknown');
            $maxAttempts = max(1, (int) config('ai.chatbot.rate_limit', 20));
            $decay = max(30, (int) config('ai.chatbot.rate_decay', 60));

            if (RateLimiter::tooManyAttempts($rateKey, $maxAttempts)) {
                return $this->failure($context, __('chat.ai.rate_limit'));
            }

            RateLimiter::hit($rateKey, $decay);

            if ($context->problemStatement === null || $context->problemStatement === '') {
                $context->problemStatement = $userMessage;
            }

            $context->quickReplies = [];

            $messages = array_merge(
                [['role' => 'system', 'content' => PublicChatPrompt::build()]],
                $this->trimHistory($history),
                [['role' => 'user', 'content' => $userMessage]],
            );

            $tools = $this->tools->definitions();
            $result = $this->complete($messages, $tools, $context);

            if ($result['success']) {
                return $result;
            }

            Log::warning('Public chatbot provider failed', [
                'provider' => $result['provider'] ?? null,
                'error' => $result['error'] ?? null,
            ]);

            return $this->catalogFallback($userMessage, $context, $result['provider'] ?? null);
        } finally {
            app()->setLocale($previousLocale);
        }
    }

    /**
     * @param  list<array<string, mixed>>  $messages
     * @param  list<array{name: string, description: string, parameters: array<string, mixed>}>  $tools
     * @return array{
     *     success: bool,
     *     reply: string,
     *     buttons: list<array{label: string, value: string}>,
     *     lead_id: ?int,
     *     provider: ?string,
     *     error: ?string
     * }
     */
    private function complete(array $messages, array $tools, PublicChatContext $context): array
    {
        $provider = null;
        $maxRounds = max(1, (int) config('ai.chatbot.max_tool_rounds', $this->limiter->maxToolRounds()));
        $options = [
            'temperature' => (float) config('ai.chatbot.temperature', 0.4),
            'max_tokens' => max(1024, (int) config('ai.chatbot.max_tokens', 2048)),
            'timeout' => max(20, (int) config('ai.chatbot.timeout', 45)),
            'thinking_budget' => 0,
        ];

        for ($round = 0; $round <= $maxRounds; $round++) {
            $result = $this->router->chat($messages, $tools, $options);
            $provider = $result['provider'] ?? $provider;

            if (! ($result['success'] ?? false)) {
                return $this->failure($context, (string) ($result['error'] ?? __('chat.ai.unavailable')), $provider);
            }

            $toolCalls = $result['tool_calls'] ?? [];
            if ($toolCalls !== []) {
                $messages[] = [
                    'role' => 'assistant',
                    'content' => $result['content'] ?? '',
                    'tool_calls' => $toolCalls,
                ];

                foreach ($toolCalls as $call) {
                    $name = (string) ($call['name'] ?? '');
                    $arguments = is_array($call['arguments'] ?? null) ? $call['arguments'] : [];
                    $toolResult = $this->tools->execute($name, $arguments, $context);
                    $payload = json_encode($toolResult->toArray(), JSON_UNESCAPED_UNICODE);

                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => (string) ($call['id'] ?? ''),
                        'name' => $name,
                        'content' => is_string($payload) ? $payload : '{"ok":false}',
                    ];
                }

                continue;
            }

            $content = trim((string) ($result['content'] ?? ''));
            if ($content === '') {
                return $this->failure($context, __('chat.ai.empty'), $provider);
            }

            return [
                'success' => true,
                'reply' => $content,
                'buttons' => $context->quickReplies,
                'lead_id' => $context->leadId,
                'provider' => $provider,
                'error' => null,
            ];
        }

        return $this->failure($context, __('chat.ai.incomplete'), $provider);
    }

    /**
     * @return array{
     *     success: bool,
     *     reply: string,
     *     buttons: list<array{label: string, value: string}>,
     *     lead_id: ?int,
     *     provider: ?string,
     *     error: ?string
     * }
     */
    private function catalogFallback(string $userMessage, PublicChatContext $context, ?string $provider): array
    {
        $items = $this->catalog->list($userMessage);
        $arabic = (bool) preg_match('/\p{Arabic}/u', $userMessage);
        $names = array_values(array_filter(array_map(
            fn (array $item): string => trim((string) ($item['title'] ?? '')),
            $items,
        )));

        if ($names === []) {
            $reply = $arabic
                ? 'يمكنني مساعدتك في خدماتنا، لكن فهرس الخدمات غير متاح الآن. اترك اسمك وبريدك وسيتواصل فريقنا معك.'
                : 'I can help with our services, but the catalog is unavailable right now. Share your name and email and our team will follow up.';
        } else {
            $list = implode($arabic ? '، ' : ', ', array_slice($names, 0, 8));
            $reply = $arabic
                ? "نعم — نقدّم خدمات تناسب طلبك، ومنها: {$list}.\nأخبرني عن مشروعك أو اطلب عرض سعر وسنربطك بفريقنا."
                : "Yes — we can help. Our published services include: {$list}.\nTell me about your project, or ask for a quote and I will connect you with the team.";
        }

        $quote = $arabic ? 'أريد عرض سعر' : 'I would like a quote';
        $more = $arabic ? 'حدثني عن مشروع الويب' : 'Tell me about a web project';

        return [
            'success' => true,
            'reply' => $reply,
            'buttons' => [
                ['label' => $arabic ? 'عرض سعر' : 'Get a quote', 'value' => $quote],
                ['label' => $arabic ? 'مشروع ويب' : 'Web project', 'value' => $more],
            ],
            'lead_id' => $context->leadId,
            'provider' => $provider,
            'error' => null,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $history
     * @return list<array<string, mixed>>
     */
    private function trimHistory(array $history): array
    {
        $max = max(4, (int) config('ai.chatbot.max_history_messages', 12));
        if (count($history) <= $max) {
            return $history;
        }

        return array_slice($history, -$max);
    }

    private function replyLocale(string $message, string $fallback): string
    {
        if (preg_match('/\p{Arabic}/u', $message)) {
            return 'ar';
        }

        return $fallback !== '' ? $fallback : app()->getLocale();
    }

    /**
     * @return array{
     *     success: bool,
     *     reply: string,
     *     buttons: list<array{label: string, value: string}>,
     *     lead_id: ?int,
     *     provider: ?string,
     *     error: string
     * }
     */
    private function failure(PublicChatContext $context, string $error, ?string $provider = null): array
    {
        return [
            'success' => false,
            'reply' => $error,
            'buttons' => [],
            'lead_id' => $context->leadId,
            'provider' => $provider,
            'error' => $error,
        ];
    }
}

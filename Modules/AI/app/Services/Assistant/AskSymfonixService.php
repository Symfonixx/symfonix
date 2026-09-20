<?php

namespace Modules\AI\Services\Assistant;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Modules\AI\Models\AiConversation;
use Modules\AI\Models\AiMessage;
use Modules\AI\Services\AiProviderRouter;
use Modules\AI\Support\AskSymfonixPrompt;
use Modules\AI\Support\AssistantErrorMapper;
use Modules\AI\Support\CostLimiter;

class AskSymfonixService
{
    public function __construct(
        private readonly AiProviderRouter $router,
        private readonly AssistantToolRegistry $tools,
        private readonly ConversationService $conversations,
        private readonly CostLimiter $limiter,
    ) {}

    /**
     * @return array{success: bool, message: ?AiMessage, error: ?string}
     */
    public function reply(User $user, AiConversation $conversation, string $userMessage): array
    {
        $this->conversations->appendUserMessage($conversation, $userMessage);

        return $this->complete($user, $conversation);
    }

    /**
     * @return array{success: bool, message: ?AiMessage, error: ?string}
     */
    public function regenerate(User $user, AiConversation $conversation): array
    {
        $lastUser = $this->conversations->removeAfterLastUserMessage($conversation);
        if ($lastUser === null) {
            return [
                'success' => false,
                'message' => null,
                'error' => __('ai::assistant.errors.empty'),
            ];
        }

        return $this->complete($user, $conversation);
    }

    /**
     * @return array{success: bool, message: ?AiMessage, error: ?string}
     */
    private function complete(User $user, AiConversation $conversation): array
    {
        if (! $this->router->isConfigured()) {
            return $this->storeFailure($conversation, __('ai::assistant.errors.not_configured'));
        }

        $tools = $this->tools->definitionsFor($user);
        $messages = array_merge(
            [['role' => 'system', 'content' => AskSymfonixPrompt::build($user)]],
            $this->conversations->historyForModel($conversation->fresh()),
        );

        $usedTools = [];
        $sources = [];
        $provider = null;

        $maxRounds = $this->limiter->maxToolRounds();

        for ($round = 0; $round <= $maxRounds; $round++) {
            $result = $this->router->chat($messages, $tools, [
                'temperature' => $this->limiter->temperature(),
                'max_tokens' => $this->limiter->maxTokens(),
                'timeout' => $this->limiter->timeout(),
            ]);

            $provider = $result['provider'] ?? $provider;

            if (! $result['success']) {
                Log::warning('Ask Symfonix provider failed', [
                    'provider' => $provider,
                    'conversation_id' => $conversation->id,
                ]);

                return $this->storeFailure(
                    $conversation,
                    AssistantErrorMapper::toUserMessage($result['error'] ?? null),
                    $provider,
                );
            }

            $toolCalls = $result['tool_calls'] ?? [];
            if ($toolCalls !== []) {
                $messages[] = [
                    'role' => 'assistant',
                    'content' => $result['content'] ?? '',
                    'tool_calls' => $toolCalls,
                ];
                $this->conversations->appendAssistantToolCall($conversation, $toolCalls, $result['content'] ?? null);

                foreach ($toolCalls as $call) {
                    $name = (string) ($call['name'] ?? '');
                    $arguments = is_array($call['arguments'] ?? null) ? $call['arguments'] : [];
                    $toolResult = $this->tools->execute($user, $name, $arguments);
                    $usedTools[] = $name;
                    $sources = array_merge($sources, $toolResult->sources);

                    $payload = json_encode($toolResult->toArray(), JSON_UNESCAPED_UNICODE);
                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => (string) ($call['id'] ?? ''),
                        'name' => $name,
                        'content' => is_string($payload) ? $payload : '{"ok":false}',
                    ];
                    $this->conversations->appendToolMessage($conversation, is_string($payload) ? $payload : '{"ok":false}', [
                        'tool_call_id' => $call['id'] ?? null,
                        'name' => $name,
                    ]);
                }

                continue;
            }

            $content = trim((string) ($result['content'] ?? ''));
            if ($content === '') {
                $content = __('ai::assistant.errors.empty');
            }

            $message = $this->conversations->appendAssistantMessage($conversation, $content, [
                'provider' => $provider,
                'tools' => array_values(array_unique($usedTools)),
                'sources' => array_values(array_unique($sources)),
            ]);

            return [
                'success' => true,
                'message' => $message,
                'error' => null,
            ];
        }

        return $this->storeFailure($conversation, __('ai::assistant.errors.incomplete'), $provider);
    }

    /**
     * @return array{success: bool, message: AiMessage, error: string}
     */
    private function storeFailure(AiConversation $conversation, string $error, ?string $provider = null): array
    {
        $message = $this->conversations->appendAssistantMessage($conversation, $error, [
            'provider' => $provider,
            'error' => true,
        ]);

        return [
            'success' => false,
            'message' => $message,
            'error' => $error,
        ];
    }
}

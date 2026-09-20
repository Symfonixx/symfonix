<?php

namespace Modules\AI\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\AI\Contracts\AiChatProvider;
use Modules\AI\Support\GeneratesEditorHtml;

class OpenAITextService implements AiChatProvider
{
    use GeneratesEditorHtml;

    /**
     * Send a prompt (optionally with selected editor content as extra
     * context) to OpenAI's chat completions API and return a rich-text
     * HTML fragment suitable for insertion into TinyMCE.
     *
     * @return array{success: bool, html: ?string, error: ?string}
     */
    public function generate(string $prompt, ?string $context = null): array
    {
        $result = $this->complete($this->systemPrompt(), $this->userMessage($prompt, $context));

        if (! $result['success'] || $result['content'] === null) {
            return $this->failureResult($result['error'] ?? __('ai::content_generation.messages.request_failed'));
        }

        return $this->successResult($result['content']);
    }

    /**
     * @return array{success: bool, content: ?string, error: ?string}
     */
    public function generateStructured(string $systemPrompt, string $userMessage): array
    {
        return $this->complete($systemPrompt, $userMessage, json: true, timeout: 90);
    }

    /**
     * @return array{success: bool, content: ?string, error: ?string}
     */
    private function complete(string $systemPrompt, string $userMessage, bool $json = false, int $timeout = 60): array
    {
        if (! $this->isConfigured()) {
            return $this->structuredFailure(__('ai::content_generation.messages.not_configured'));
        }

        $payload = [
            'model' => config('services.openai.model', 'gpt-4o-mini'),
            'temperature' => 0.7,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ],
        ];

        if ($json) {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        try {
            $response = Http::timeout($timeout)
                ->withToken((string) config('services.openai.api_key'))
                ->asJson()
                ->post($this->endpoint(), $payload)
                ->throw();

            $content = $response->json('choices.0.message.content');

            if (! is_string($content) || trim($content) === '') {
                return $this->structuredFailure(__('ai::content_generation.messages.empty_result'));
            }

            return [
                'success' => true,
                'content' => $content,
                'error' => null,
            ];
        } catch (RequestException $e) {
            $error = $e->response?->json('error.message') ?? $e->getMessage();

            Log::error('OpenAI content generation request failed', [
                'error' => $error,
            ]);

            return $this->structuredFailure(
                is_string($error) ? $error : __('ai::content_generation.messages.request_failed')
            );
        } catch (\Throwable $e) {
            report($e);

            return $this->structuredFailure($e->getMessage());
        }
    }

    /**
     * @return array{success: bool, content: ?string, error: ?string}
     */
    private function structuredFailure(string $error): array
    {
        return [
            'success' => false,
            'content' => null,
            'error' => $error,
        ];
    }

    public function isConfigured(): bool
    {
        return filled(config('services.openai.api_key'));
    }

    public function providerName(): string
    {
        return 'openai';
    }

    /**
     * @param  list<array<string, mixed>>  $messages
     * @param  list<array{name: string, description: string, parameters: array<string, mixed>}>  $tools
     * @param  array{temperature?: float, max_tokens?: int, timeout?: int}  $options
     * @return array{success: bool, content: ?string, tool_calls: list<array{id: string, name: string, arguments: array<string, mixed>}>, error: ?string, provider: string}
     */
    public function chat(array $messages, array $tools = [], array $options = []): array
    {
        if (! $this->isConfigured()) {
            return $this->chatFailure(__('ai::assistant.errors.not_configured'));
        }

        $payload = [
            'model' => config('services.openai.model', 'gpt-4o-mini'),
            'temperature' => $options['temperature'] ?? (float) config('ai.assistant.temperature', 0.3),
            'max_tokens' => $options['max_tokens'] ?? (int) config('ai.assistant.max_tokens', 1200),
            'messages' => $this->mapChatMessages($messages),
        ];

        if ($tools !== []) {
            $payload['tools'] = $this->mapTools($tools);
            $payload['tool_choice'] = 'auto';
        }

        $timeout = (int) ($options['timeout'] ?? config('ai.assistant.timeout', 90));

        try {
            $response = Http::timeout($timeout)
                ->withToken((string) config('services.openai.api_key'))
                ->asJson()
                ->post($this->endpoint(), $payload)
                ->throw();

            $message = $response->json('choices.0.message');
            if (! is_array($message)) {
                return $this->chatFailure(__('ai::assistant.errors.empty'));
            }

            $toolCalls = $this->parseToolCalls($message['tool_calls'] ?? []);
            $content = $message['content'] ?? null;
            $content = is_string($content) ? $content : null;

            if ($toolCalls === [] && ($content === null || trim($content) === '')) {
                return $this->chatFailure(__('ai::assistant.errors.empty'));
            }

            return [
                'success' => true,
                'content' => $content,
                'tool_calls' => $toolCalls,
                'error' => null,
                'provider' => $this->providerName(),
            ];
        } catch (RequestException $e) {
            $status = $e->response?->status();
            $error = $e->response?->json('error.message') ?? $e->getMessage();

            Log::error('OpenAI assistant chat request failed', [
                'status' => $status,
                'error' => is_string($error) ? $error : 'request_failed',
            ]);

            return $this->chatFailure($this->classifyHttpError($status, is_string($error) ? $error : null));
        } catch (\Throwable $e) {
            report($e);

            return $this->chatFailure(__('ai::assistant.errors.connect'));
        }
    }

    /**
     * @param  list<array<string, mixed>>  $messages
     * @return list<array<string, mixed>>
     */
    private function mapChatMessages(array $messages): array
    {
        $mapped = [];

        foreach ($messages as $message) {
            $role = (string) ($message['role'] ?? 'user');

            if ($role === 'tool') {
                $mapped[] = [
                    'role' => 'tool',
                    'tool_call_id' => (string) ($message['tool_call_id'] ?? ''),
                    'content' => (string) ($message['content'] ?? ''),
                ];

                continue;
            }

            $row = [
                'role' => $role,
                'content' => $message['content'] ?? '',
            ];

            if ($role === 'assistant' && ! empty($message['tool_calls']) && is_array($message['tool_calls'])) {
                $row['tool_calls'] = array_map(function (array $call): array {
                    $arguments = $call['arguments'] ?? new \stdClass;

                    return [
                        'id' => (string) ($call['id'] ?? ''),
                        'type' => 'function',
                        'function' => [
                            'name' => (string) ($call['name'] ?? ''),
                            'arguments' => is_string($arguments)
                                ? $arguments
                                : json_encode($arguments, JSON_UNESCAPED_UNICODE),
                        ],
                    ];
                }, $message['tool_calls']);

                if ($row['content'] === '') {
                    $row['content'] = null;
                }
            }

            $mapped[] = $row;
        }

        return $mapped;
    }

    /**
     * @param  list<array{name: string, description: string, parameters: array<string, mixed>}>  $tools
     * @return list<array<string, mixed>>
     */
    private function mapTools(array $tools): array
    {
        return array_map(static function (array $tool): array {
            $parameters = $tool['parameters'] ?? ['type' => 'object', 'properties' => new \stdClass];
            if (($parameters['properties'] ?? null) === []) {
                $parameters['properties'] = new \stdClass;
            }

            return [
                'type' => 'function',
                'function' => [
                    'name' => $tool['name'],
                    'description' => $tool['description'],
                    'parameters' => $parameters,
                ],
            ];
        }, $tools);
    }

    /**
     * @return list<array{id: string, name: string, arguments: array<string, mixed>}>
     */
    private function parseToolCalls(mixed $toolCalls): array
    {
        if (! is_array($toolCalls)) {
            return [];
        }

        $parsed = [];

        foreach ($toolCalls as $call) {
            if (! is_array($call)) {
                continue;
            }

            $arguments = $call['function']['arguments'] ?? '{}';
            $decoded = is_string($arguments) ? json_decode($arguments, true) : $arguments;

            $parsed[] = [
                'id' => (string) ($call['id'] ?? uniqid('call_', true)),
                'name' => (string) ($call['function']['name'] ?? ''),
                'arguments' => is_array($decoded) ? $decoded : [],
            ];
        }

        return $parsed;
    }

    /**
     * @return array{success: bool, content: ?string, tool_calls: list<array{id: string, name: string, arguments: array<string, mixed>}>, error: ?string, provider: string}
     */
    private function chatFailure(string $error): array
    {
        return [
            'success' => false,
            'content' => null,
            'tool_calls' => [],
            'error' => $error,
            'provider' => $this->providerName(),
        ];
    }

    private function classifyHttpError(?int $status, ?string $error): string
    {
        if ($status === 429 || ($error !== null && str_contains(strtolower($error), 'rate'))) {
            return __('ai::assistant.errors.rate_limit');
        }

        if (in_array($status, [401, 403], true)) {
            return __('ai::assistant.errors.not_configured');
        }

        return __('ai::assistant.errors.connect');
    }

    private function endpoint(): string
    {
        $baseUrl = rtrim((string) config('services.openai.base_url', 'https://api.openai.com/v1'), '/');

        return "{$baseUrl}/chat/completions";
    }
}

<?php

namespace Modules\AI\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\AI\Contracts\AiChatProvider;
use Modules\AI\Support\GeneratesEditorHtml;

class GeminiTextService implements AiChatProvider
{
    use GeneratesEditorHtml;

    /**
     * Generate a TinyMCE HTML fragment using Gemini's analysis/text model.
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

        $generationConfig = [
            'temperature' => 0.7,
        ];

        if ($json) {
            $generationConfig['responseMimeType'] = 'application/json';
        }

        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemPrompt],
                ],
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $userMessage],
                    ],
                ],
            ],
            'generationConfig' => $generationConfig,
        ];

        try {
            $response = Http::timeout($timeout)
                ->asJson()
                ->post($this->endpoint(), $payload)
                ->throw();

            $content = $this->extractText($response->json());

            if ($content === null) {
                return $this->structuredFailure(__('ai::content_generation.messages.empty_result'));
            }

            return [
                'success' => true,
                'content' => $content,
                'error' => null,
            ];
        } catch (RequestException $e) {
            $error = $e->response?->json('error.message') ?? $e->getMessage();

            Log::error('Gemini content generation request failed', [
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
        return filled(config('services.gemini.api_key'));
    }

    public function providerName(): string
    {
        return 'gemini';
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

        [$system, $contents] = $this->mapChatMessages($messages);

        $generationConfig = [
            'temperature' => $options['temperature'] ?? (float) config('ai.assistant.temperature', 0.3),
            'maxOutputTokens' => $options['max_tokens'] ?? (int) config('ai.assistant.max_tokens', 1200),
        ];

        if (array_key_exists('thinking_budget', $options)) {
            $generationConfig['thinkingConfig'] = [
                'thinkingBudget' => max(0, (int) $options['thinking_budget']),
            ];
        }

        $payload = [
            'contents' => $contents,
            'generationConfig' => $generationConfig,
        ];

        if ($system !== '') {
            $payload['systemInstruction'] = [
                'parts' => [
                    ['text' => $system],
                ],
            ];
        }

        if ($tools !== []) {
            $payload['tools'] = [
                ['functionDeclarations' => $this->mapTools($tools)],
            ];
        }

        $timeout = (int) ($options['timeout'] ?? config('ai.assistant.timeout', 90));

        try {
            $response = Http::timeout($timeout)
                ->asJson()
                ->post($this->endpoint(), $payload)
                ->throw();

            $json = $response->json();
            $candidate = is_array($json) ? ($json['candidates'][0] ?? []) : [];
            $parts = is_array($candidate) ? ($candidate['content']['parts'] ?? []) : [];
            $toolCalls = $this->parseToolCalls($parts);
            $content = $this->extractTextFromParts(is_array($parts) ? $parts : []);

            if ($toolCalls === [] && ($content === null || trim($content) === '')) {
                Log::warning('Gemini assistant chat returned no text', [
                    'finish_reason' => is_array($candidate) ? ($candidate['finishReason'] ?? null) : null,
                ]);

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

            Log::error('Gemini assistant chat request failed', [
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
     * @return array{0: string, 1: list<array<string, mixed>>}
     */
    private function mapChatMessages(array $messages): array
    {
        $system = '';
        $contents = [];

        foreach ($messages as $message) {
            $role = (string) ($message['role'] ?? 'user');

            if ($role === 'system') {
                $text = trim((string) ($message['content'] ?? ''));
                if ($text !== '') {
                    $system = $system === '' ? $text : $system."\n".$text;
                }

                continue;
            }

            if ($role === 'tool') {
                $contents[] = [
                    'role' => 'user',
                    'parts' => [[
                        'functionResponse' => [
                            'name' => (string) ($message['name'] ?? 'unknown'),
                            'response' => $this->decodeToolContent($message['content'] ?? '{}'),
                        ],
                    ]],
                ];

                continue;
            }

            if ($role === 'assistant' && ! empty($message['tool_calls']) && is_array($message['tool_calls'])) {
                $parts = [];
                $text = trim((string) ($message['content'] ?? ''));
                if ($text !== '') {
                    $parts[] = ['text' => $text];
                }

                foreach ($message['tool_calls'] as $call) {
                    $args = $call['arguments'] ?? new \stdClass;
                    $parts[] = [
                        'functionCall' => [
                            'name' => (string) ($call['name'] ?? ''),
                            'args' => $args === [] ? new \stdClass : $args,
                        ],
                    ];
                }

                $contents[] = [
                    'role' => 'model',
                    'parts' => $parts,
                ];

                continue;
            }

            $contents[] = [
                'role' => $role === 'assistant' ? 'model' : 'user',
                'parts' => [
                    ['text' => (string) ($message['content'] ?? '')],
                ],
            ];
        }

        return [$system, $contents];
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
                'name' => $tool['name'],
                'description' => $tool['description'],
                'parameters' => $parameters,
            ];
        }, $tools);
    }

    /**
     * @return list<array{id: string, name: string, arguments: array<string, mixed>}>
     */
    private function parseToolCalls(mixed $parts): array
    {
        if (! is_array($parts)) {
            return [];
        }

        $parsed = [];

        foreach ($parts as $index => $part) {
            if (! is_array($part) || ! isset($part['functionCall']) || ! is_array($part['functionCall'])) {
                continue;
            }

            $call = $part['functionCall'];
            $args = $call['args'] ?? [];

            $parsed[] = [
                'id' => (string) ($call['id'] ?? 'call_'.$index.'_'.($call['name'] ?? 'tool')),
                'name' => (string) ($call['name'] ?? ''),
                'arguments' => is_array($args) ? $args : [],
            ];
        }

        return $parsed;
    }

    /**
     * @param  list<array<string, mixed>>  $parts
     */
    private function extractTextFromParts(array $parts): ?string
    {
        $chunks = [];

        foreach ($parts as $part) {
            if (isset($part['text']) && is_string($part['text']) && trim($part['text']) !== '') {
                $chunks[] = $part['text'];
            }
        }

        if ($chunks === []) {
            return null;
        }

        return implode("\n", $chunks);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeToolContent(mixed $content): array
    {
        if (is_array($content)) {
            return $content;
        }

        if (! is_string($content) || trim($content) === '') {
            return ['result' => ''];
        }

        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : ['result' => $content];
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

    /**
     * @param  array<string, mixed>|null  $responseJson
     */
    private function extractText(?array $responseJson): ?string
    {
        $parts = $responseJson['candidates'][0]['content']['parts'] ?? [];
        $chunks = [];

        foreach ($parts as $part) {
            if (isset($part['text']) && is_string($part['text']) && trim($part['text']) !== '') {
                $chunks[] = $part['text'];
            }
        }

        if ($chunks === []) {
            return null;
        }

        return implode("\n", $chunks);
    }

    private function endpoint(): string
    {
        $baseUrl = rtrim((string) config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $model = config('services.gemini.analysis_model', 'gemini-2.5-flash');
        $apiKey = (string) config('services.gemini.api_key');

        return "{$baseUrl}/models/{$model}:generateContent?key={$apiKey}";
    }
}

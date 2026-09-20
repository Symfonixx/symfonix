<?php

namespace Modules\AI\Services;

use Modules\Base\Support\AskSymfonixConfig;

class AiProviderRouter
{
    public function __construct(
        private readonly OpenAITextService $openAITextService,
        private readonly GeminiTextService $geminiTextService,
    ) {}

    public function isConfigured(): bool
    {
        return $this->openAITextService->isConfigured() || $this->geminiTextService->isConfigured();
    }

    /**
     * @param  list<array<string, mixed>>  $messages
     * @param  list<array{name: string, description: string, parameters: array<string, mixed>}>  $tools
     * @param  array{temperature?: float, max_tokens?: int, timeout?: int}  $options
     * @return array{success: bool, content: ?string, tool_calls: list<array{id: string, name: string, arguments: array<string, mixed>}>, error: ?string, provider: string}
     */
    public function chat(array $messages, array $tools = [], array $options = []): array
    {
        $preference = AskSymfonixConfig::provider();

        if ($preference === 'openai') {
            return $this->openAITextService->chat($messages, $tools, $options);
        }

        if ($preference === 'gemini') {
            return $this->geminiTextService->chat($messages, $tools, $options);
        }

        if ($this->openAITextService->isConfigured()) {
            $result = $this->openAITextService->chat($messages, $tools, $options);
            if ($result['success'] || ! $this->geminiTextService->isConfigured()) {
                return $result;
            }

            return $this->geminiTextService->chat($messages, $tools, $options);
        }

        if ($this->geminiTextService->isConfigured()) {
            return $this->geminiTextService->chat($messages, $tools, $options);
        }

        return [
            'success' => false,
            'content' => null,
            'tool_calls' => [],
            'error' => __('ai::assistant.errors.not_configured'),
            'provider' => 'none',
        ];
    }
}

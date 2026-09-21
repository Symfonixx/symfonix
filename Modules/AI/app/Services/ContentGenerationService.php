<?php

namespace Modules\AI\Services;

use Illuminate\Support\Facades\Log;
use Modules\AI\Support\FormContentSchema;

class ContentGenerationService
{
    public function __construct(
        private readonly OpenAITextService $openAITextService,
        private readonly GeminiTextService $geminiTextService,
    ) {}

    /**
     * Prefer OpenAI for editor content. If OpenAI is missing or the request
     * fails, fall back to Gemini's analysis model.
     *
     * @return array{success: bool, html: ?string, error: ?string, provider: ?string}
     */
    public function generate(string $prompt, ?string $context = null): array
    {
        return $this->attemptProviders(
            fn (object $provider): array => $provider->generate($prompt, $context),
        );
    }

    /**
     * Generate structured values for an admin content form.
     *
     * @param  array<string, mixed>|null  $existing
     * @return array{success: bool, fields: ?array<string, string>, error: ?string, provider: ?string}
     */
    public function generateForm(
        string $formType,
        string $prompt,
        ?string $locale = null,
        ?array $existing = null,
        string $mode = FormContentSchema::MODE_CREATE,
    ): array {
        if (FormContentSchema::get($formType) === null) {
            return $this->formFailure(__('ai::content_generation.messages.invalid_form'));
        }

        $mode = FormContentSchema::normalizeMode($mode);
        $existing = FormContentSchema::filledExisting($existing);

        if ($mode === FormContentSchema::MODE_OPTIMIZE && $existing === []) {
            return $this->formFailure(__('ai::content_generation.messages.empty_content'));
        }

        $result = $this->generateJson(
            FormContentSchema::systemPrompt($formType, $locale ?: app()->getLocale(), $mode),
            FormContentSchema::userMessage($prompt, $existing, $mode),
        );

        if (! $result['success']) {
            return $this->formFailure(
                $result['error'] ?? __('ai::content_generation.messages.request_failed'),
                $result['provider'],
            );
        }

        $fields = FormContentSchema::normalize($formType, $result['data'] ?? []);

        if (! FormContentSchema::hasContent($fields)) {
            return $this->formFailure(
                __('ai::content_generation.messages.empty_result'),
                $result['provider'],
            );
        }

        return [
            'success' => true,
            'fields' => $fields,
            'error' => null,
            'provider' => $result['provider'],
        ];
    }

    /**
     * Prefer OpenAI for JSON generation, then fall back to Gemini.
     *
     * @return array{success: bool, content: ?string, error: ?string, provider: ?string}
     */
    private function generateStructuredContent(string $systemPrompt, string $userMessage): array
    {
        return $this->attemptProviders(
            fn (object $provider): array => $provider->generateStructured($systemPrompt, $userMessage),
            static fn (array $result): bool => ($result['success'] ?? false) && is_string($result['content'] ?? null),
        );
    }

    /**
     * Decode a JSON object from the first available provider.
     *
     * @return array{success: bool, data: ?array<string, mixed>, error: ?string, provider: ?string}
     */
    public function generateJson(string $systemPrompt, string $userMessage): array
    {
        $result = $this->generateStructuredContent($systemPrompt, $userMessage);

        if (! $result['success'] || ! is_string($result['content'] ?? null)) {
            return [
                'success' => false,
                'data' => null,
                'error' => $result['error'] ?? __('ai::content_generation.messages.request_failed'),
                'provider' => $result['provider'] ?? null,
            ];
        }

        $decoded = FormContentSchema::decode($result['content']);

        if ($decoded === null) {
            Log::warning('AI structured JSON could not be decoded', [
                'provider' => $result['provider'] ?? null,
                'snippet' => mb_substr($result['content'], 0, 500),
            ]);

            return [
                'success' => false,
                'data' => null,
                'error' => __('ai::content_generation.messages.empty_result'),
                'provider' => $result['provider'],
            ];
        }

        return [
            'success' => true,
            'data' => $decoded,
            'error' => null,
            'provider' => $result['provider'],
        ];
    }

    /**
     * @param  callable(OpenAITextService|GeminiTextService): array<string, mixed>  $callback
     * @param  (callable(array<string, mixed>): bool)|null  $isSuccess
     * @return array<string, mixed>
     */
    private function attemptProviders(callable $callback, ?callable $isSuccess = null): array
    {
        $isSuccess ??= static fn (array $result): bool => (bool) ($result['success'] ?? false);
        $openAiConfigured = $this->openAITextService->isConfigured();
        $geminiConfigured = $this->geminiTextService->isConfigured();

        if (! $openAiConfigured && ! $geminiConfigured) {
            return [
                'success' => false,
                'html' => null,
                'content' => null,
                'error' => __('ai::content_generation.messages.not_configured'),
                'provider' => null,
            ];
        }

        $provider = null;
        $lastResult = null;

        if ($openAiConfigured) {
            $provider = 'openai';
            $lastResult = $callback($this->openAITextService);

            if ($isSuccess($lastResult)) {
                return $this->withProvider($lastResult, $provider);
            }

            Log::warning('OpenAI content generation failed, falling back to Gemini', [
                'error' => $lastResult['error'] ?? null,
            ]);
        }

        if ($geminiConfigured) {
            $provider = 'gemini';
            $lastResult = $callback($this->geminiTextService);

            if ($isSuccess($lastResult)) {
                return $this->withProvider($lastResult, $provider);
            }
        }

        return $this->withProvider($lastResult ?? [
            'success' => false,
            'html' => null,
            'content' => null,
            'error' => __('ai::content_generation.messages.request_failed'),
        ], $provider);
    }

    /**
     * @return array{success: bool, fields: null, error: string, provider: ?string}
     */
    private function formFailure(string $error, ?string $provider = null): array
    {
        return [
            'success' => false,
            'fields' => null,
            'error' => $error,
            'provider' => $provider,
        ];
    }

    /**
     * @param  array<string, mixed>  $result
     * @return array<string, mixed>
     */
    private function withProvider(array $result, ?string $provider): array
    {
        $result['provider'] = $provider;

        return $result;
    }
}

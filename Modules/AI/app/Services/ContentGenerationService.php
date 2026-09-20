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
        $openAiConfigured = $this->openAITextService->isConfigured();
        $geminiConfigured = $this->geminiTextService->isConfigured();

        if (! $openAiConfigured && ! $geminiConfigured) {
            return $this->withProvider(
                [
                    'success' => false,
                    'html' => null,
                    'error' => __('ai::content_generation.messages.not_configured'),
                ],
                null,
            );
        }

        if ($openAiConfigured) {
            $result = $this->openAITextService->generate($prompt, $context);

            if ($result['success']) {
                return $this->withProvider($result, 'openai');
            }

            Log::warning('OpenAI content generation failed, falling back to Gemini', [
                'error' => $result['error'],
            ]);

            if (! $geminiConfigured) {
                return $this->withProvider($result, 'openai');
            }
        }

        $result = $this->geminiTextService->generate($prompt, $context);

        return $this->withProvider($result, 'gemini');
    }

    /**
     * Generate structured values for an admin content form.
     *
     * @param  array<string, string>|null  $existing
     * @return array{success: bool, fields: ?array<string, string>, error: ?string, provider: ?string}
     */
    public function generateForm(string $formType, string $prompt, ?string $locale = null, ?array $existing = null): array
    {
        if (FormContentSchema::get($formType) === null) {
            return [
                'success' => false,
                'fields' => null,
                'error' => __('ai::content_generation.messages.invalid_form'),
                'provider' => null,
            ];
        }

        $result = $this->generateStructuredContent(
            FormContentSchema::systemPrompt($formType, $locale ?: app()->getLocale()),
            FormContentSchema::userMessage($prompt, $existing),
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

        $fields = FormContentSchema::normalize($formType, $decoded);
        $hasContent = collect($fields)->contains(fn (string $value): bool => $value !== '');

        if (! $hasContent) {
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

    /**
     * Prefer OpenAI for JSON generation, then fall back to Gemini.
     *
     * @return array{success: bool, content: ?string, error: ?string, provider: ?string}
     */
    public function generateStructuredContent(string $systemPrompt, string $userMessage): array
    {
        $openAiConfigured = $this->openAITextService->isConfigured();
        $geminiConfigured = $this->geminiTextService->isConfigured();

        if (! $openAiConfigured && ! $geminiConfigured) {
            return [
                'success' => false,
                'content' => null,
                'error' => __('ai::content_generation.messages.not_configured'),
                'provider' => null,
            ];
        }

        $provider = null;
        $raw = null;
        $error = __('ai::content_generation.messages.request_failed');

        if ($openAiConfigured) {
            $result = $this->openAITextService->generateStructured($systemPrompt, $userMessage);
            $provider = 'openai';

            if ($result['success'] && is_string($result['content'])) {
                $raw = $result['content'];
            } else {
                $error = $result['error'] ?? $error;
                Log::warning('OpenAI structured generation failed, falling back to Gemini', [
                    'error' => $error,
                ]);
            }
        }

        if ($raw === null && $geminiConfigured) {
            $result = $this->geminiTextService->generateStructured($systemPrompt, $userMessage);
            $provider = 'gemini';

            if ($result['success'] && is_string($result['content'])) {
                $raw = $result['content'];
            } else {
                $error = $result['error'] ?? $error;
            }
        }

        if ($raw === null) {
            return [
                'success' => false,
                'content' => null,
                'error' => $error,
                'provider' => $provider,
            ];
        }

        return [
            'success' => true,
            'content' => $raw,
            'error' => null,
            'provider' => $provider,
        ];
    }

    /**
     * @param  array{success: bool, html: ?string, error: ?string}  $result
     * @return array{success: bool, html: ?string, error: ?string, provider: ?string}
     */
    private function withProvider(array $result, ?string $provider): array
    {
        $result['provider'] = $provider;

        return $result;
    }
}

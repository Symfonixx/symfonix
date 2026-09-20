<?php

namespace Modules\AI\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\AI\Support\CompanyContentProfile;

class GeminiImageService
{
    /**
     * Send an image + edit instruction to the Gemini image model and return
     * the generated image binary.
     *
     * @param  array{name?: string, binary: string, mime_type: string}|null  $brandLogo
     * @return array{success: bool, binary: ?string, mime_type: ?string, error: ?string}
     */
    public function editImage(string $imageContents, string $mimeType, string $prompt, ?array $brandLogo = null): array
    {
        $parts = [
            [
                'inlineData' => [
                    'mimeType' => $mimeType,
                    'data' => base64_encode($imageContents),
                ],
            ],
        ];

        if ($brandLogo !== null) {
            $parts[] = $this->inlineImagePart($brandLogo);
        }

        $parts[] = [
            'text' => $this->withBrandContext($prompt, 'edit', $brandLogo),
        ];

        return $this->requestImage($parts);
    }

    /**
     * Generate a brand new image from a text prompt. When a brand logo is
     * supplied it is sent as a visual identity reference.
     *
     * @param  array{name?: string, binary: string, mime_type: string}|null  $brandLogo
     * @return array{success: bool, binary: ?string, mime_type: ?string, error: ?string}
     */
    public function generateImage(string $prompt, ?array $brandLogo = null): array
    {
        $parts = [];

        if ($brandLogo !== null) {
            $parts[] = $this->inlineImagePart($brandLogo);
        }

        $parts[] = [
            'text' => $this->withBrandContext($prompt, 'create', $brandLogo),
        ];

        return $this->requestImage($parts);
    }

    public function isConfigured(): bool
    {
        return filled(config('services.gemini.api_key'));
    }

    /**
     * @param  array{name?: string, binary: string, mime_type: string}  $brandLogo
     * @return array<string, mixed>
     */
    private function inlineImagePart(array $brandLogo): array
    {
        return [
            'inlineData' => [
                'mimeType' => $brandLogo['mime_type'] ?: 'image/png',
                'data' => base64_encode($brandLogo['binary']),
            ],
        ];
    }

    /**
     * @param  array{name?: string, binary: string, mime_type: string}|null  $brandLogo
     */
    private function withBrandContext(string $prompt, string $mode, ?array $brandLogo): string
    {
        $name = is_string($brandLogo['name'] ?? null) && $brandLogo['name'] !== ''
            ? $brandLogo['name']
            : (string) config('app.name', 'the company');

        $instructions = $mode === 'create'
            ? ($brandLogo === null
                ? "Create an image that matches {$name}'s brand voice and positioning."
                : "The attached image is the official brand logo for {$name}. Match its colors, shapes, and visual identity. Incorporate the logo into the composition only when it fits the request; otherwise use it as a style and color reference. Stay on-brand.")
            : ($brandLogo === null
                ? "Edit the attached photo while staying consistent with {$name}'s brand."
                : "The first attached image is the photo to edit. The second attached image is the official brand logo for {$name} (identity reference only). Keep the result on-brand: match colors, style, and visual identity. Do not replace the subject with the logo unless the user asked to include the logo.");

        $profile = CompanyContentProfile::promptBlock();
        $block = $profile !== '' ? $profile."\n\n" : '';

        return $instructions."\n\n".$block."User request:\n".$prompt;
    }

    /**
     * @param  array<int, array<string, mixed>>  $parts
     * @return array{success: bool, binary: ?string, mime_type: ?string, error: ?string}
     */
    private function requestImage(array $parts): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'binary' => null,
                'mime_type' => null,
                'error' => __('ai::image_edit.messages.not_configured'),
            ];
        }

        $payload = [
            'contents' => [
                ['parts' => $parts],
            ],
            'generationConfig' => [
                'responseModalities' => ['IMAGE'],
            ],
        ];

        try {
            $response = Http::timeout(60)
                ->asJson()
                ->post($this->endpoint(), $payload)
                ->throw();

            $part = $this->extractInlineImagePart($response->json());

            if ($part === null) {
                return [
                    'success' => false,
                    'binary' => null,
                    'mime_type' => null,
                    'error' => __('ai::image_edit.messages.no_image_returned'),
                ];
            }

            return [
                'success' => true,
                'binary' => base64_decode($part['data'], true) ?: null,
                'mime_type' => $part['mimeType'] ?? 'image/png',
                'error' => null,
            ];
        } catch (RequestException $e) {
            $error = $e->response?->json('error.message') ?? $e->getMessage();

            Log::error('Gemini image request failed', [
                'error' => $error,
            ]);

            return [
                'success' => false,
                'binary' => null,
                'mime_type' => null,
                'error' => is_string($error) ? $error : __('ai::image_edit.messages.request_failed'),
            ];
        } catch (\Throwable $e) {
            report($e);

            return [
                'success' => false,
                'binary' => null,
                'mime_type' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Extract the first inline image part (mimeType + base64 data) from a
     * Gemini generateContent response payload.
     *
     * @param  array<string, mixed>|null  $responseJson
     * @return array{mimeType: string, data: string}|null
     */
    private function extractInlineImagePart(?array $responseJson): ?array
    {
        $parts = $responseJson['candidates'][0]['content']['parts'] ?? [];

        foreach ($parts as $part) {
            $inline = $part['inlineData'] ?? $part['inline_data'] ?? null;

            if (is_array($inline) && isset($inline['data'])) {
                return [
                    'mimeType' => $inline['mimeType'] ?? $inline['mime_type'] ?? 'image/png',
                    'data' => $inline['data'],
                ];
            }
        }

        return null;
    }

    private function endpoint(): string
    {
        $baseUrl = rtrim((string) config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $model = config('services.gemini.image_model', 'gemini-2.5-flash-image');
        $apiKey = (string) config('services.gemini.api_key');

        return "{$baseUrl}/models/{$model}:generateContent?key={$apiKey}";
    }
}

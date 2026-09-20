<?php

namespace Modules\CRM\Services\Marketing;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Modules\Base\Support\WhatsAppConfig;
use Modules\CRM\Models\WhatsAppTemplate;

class WhatsAppApiService
{
    public function __construct(
        private readonly WhatsAppTemplateService $templateService,
    ) {}

    /**
     * @param  array<int|string, mixed>  $parameters
     * @return array{success: bool, message_id: ?string, error: ?string}
     */
    public function sendTemplateMessage(
        string $phone,
        WhatsAppTemplate $template,
        array $parameters = [],
    ): array {
        if (! WhatsAppConfig::isConfigured()) {
            return [
                'success' => false,
                'message_id' => null,
                'error' => __('crm::whatsapp.messages.not_configured'),
            ];
        }

        $payload = $this->buildTemplatePayload($phone, $template, $parameters);

        try {
            $response = Http::withToken((string) config('services.whatsapp.api_token'))
                ->timeout(30)
                ->post($this->messagesEndpoint(), $payload)
                ->throw();

            $messageId = $response->json('messages.0.id');

            return [
                'success' => true,
                'message_id' => is_string($messageId) ? $messageId : null,
                'error' => null,
            ];
        } catch (RequestException $e) {
            $error = $e->response?->json('error.message')
                ?? $e->response?->json('error.error_user_msg')
                ?? $e->getMessage();

            return [
                'success' => false,
                'message_id' => null,
                'error' => is_string($error) ? $error : __('crm::whatsapp.messages.send_failed'),
            ];
        } catch (\Throwable $e) {
            report($e);

            return [
                'success' => false,
                'message_id' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * @param  array<int|string, mixed>  $parameters
     * @return array<int, array<string, mixed>>
     */
    public function buildComponents(WhatsAppTemplate $template, array $parameters): array
    {
        $normalized = $this->templateService->normalizeParameters($parameters, $template);
        $components = [];

        $headerComponent = $this->headerComponent($template, $normalized['header']);
        if ($headerComponent !== null) {
            $components[] = $headerComponent;
        }

        if (! empty($normalized['body'])) {
            $components[] = [
                'type' => 'body',
                'parameters' => collect($normalized['body'])
                    ->map(fn (string $value) => ['type' => 'text', 'text' => $value])
                    ->values()
                    ->all(),
            ];
        }

        foreach ($normalized['buttons'] as $buttonIndex => $buttonParameters) {
            if ($buttonParameters === []) {
                continue;
            }

            $components[] = [
                'type' => 'button',
                'sub_type' => 'url',
                'index' => (string) $buttonIndex,
                'parameters' => collect($buttonParameters)
                    ->map(fn (string $value) => ['type' => 'text', 'text' => $value])
                    ->values()
                    ->all(),
            ];
        }

        return $components;
    }

    /**
     * @param  array<int|string, mixed>  $parameters
     * @return array<string, mixed>
     */
    public function buildTemplatePayload(
        string $phone,
        WhatsAppTemplate $template,
        array $parameters,
    ): array {
        return [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => $template->name,
                'language' => ['code' => $template->language],
                'components' => $this->buildComponents($template, $parameters),
            ],
        ];
    }

    /**
     * @param  array<int, string>  $headerParameters
     * @return array<string, mixed>|null
     */
    private function headerComponent(WhatsAppTemplate $template, array $headerParameters): ?array
    {
        if ($template->header_type === WhatsAppTemplate::HEADER_NONE) {
            return null;
        }

        if ($template->header_type === WhatsAppTemplate::HEADER_TEXT) {
            if ($headerParameters === []) {
                return null;
            }

            return [
                'type' => 'header',
                'parameters' => collect($headerParameters)
                    ->map(fn (string $value) => ['type' => 'text', 'text' => $value])
                    ->values()
                    ->all(),
            ];
        }

        if (! filled($template->header_content)) {
            return null;
        }

        return [
            'type' => 'header',
            'parameters' => [
                [
                    'type' => $template->header_type,
                    $template->header_type => ['link' => $template->header_content],
                ],
            ],
        ];
    }

    private function messagesEndpoint(): string
    {
        $version = config('services.whatsapp.api_version', 'v21.0');
        $phoneNumberId = config('services.whatsapp.phone_number_id');

        return "https://graph.facebook.com/{$version}/{$phoneNumberId}/messages";
    }
}

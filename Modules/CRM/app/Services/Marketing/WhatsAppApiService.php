<?php

namespace Modules\CRM\Services\Marketing;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Modules\Base\Support\WhatsAppConfig;
use Modules\CRM\Models\WhatsAppTemplate;

class WhatsAppApiService
{
    /**
     * @param  array<int, string>  $bodyParameters
     * @param  array<int, string>  $headerParameters
     * @return array{success: bool, message_id: ?string, error: ?string}
     */
    public function sendTemplateMessage(
        string $phone,
        WhatsAppTemplate $template,
        array $bodyParameters = [],
        array $headerParameters = [],
    ): array {
        if (! WhatsAppConfig::isConfigured()) {
            return [
                'success' => false,
                'message_id' => null,
                'error' => __('crm::whatsapp.messages.not_configured'),
            ];
        }

        $payload = $this->buildTemplatePayload($phone, $template, $bodyParameters, $headerParameters);

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
     * @param  array<int, string>  $bodyParameters
     * @param  array<int, string>  $headerParameters
     * @return array<string, mixed>
     */
    private function buildTemplatePayload(
        string $phone,
        WhatsAppTemplate $template,
        array $bodyParameters,
        array $headerParameters,
    ): array {
        $components = [];

        if ($template->header_type !== WhatsAppTemplate::HEADER_NONE && filled($template->header_content)) {
            $headerComponent = ['type' => 'header'];

            if ($template->header_type === WhatsAppTemplate::HEADER_TEXT) {
                $headerComponent['parameters'] = [
                    ['type' => 'text', 'text' => $headerParameters[1] ?? $template->header_content],
                ];
            } else {
                $headerComponent['parameters'] = [
                    [
                        'type' => $template->header_type,
                        $template->header_type => ['link' => $template->header_content],
                    ],
                ];
            }

            $components[] = $headerComponent;
        }

        if (! empty($bodyParameters)) {
            $components[] = [
                'type' => 'body',
                'parameters' => collect($bodyParameters)
                    ->values()
                    ->map(fn (string $value) => ['type' => 'text', 'text' => $value])
                    ->all(),
            ];
        }

        return [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => $template->name,
                'language' => ['code' => $template->language],
                'components' => $components,
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

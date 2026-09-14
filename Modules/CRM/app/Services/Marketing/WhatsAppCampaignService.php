<?php

namespace Modules\CRM\Services\Marketing;

use Illuminate\Support\Collection;
use Modules\Base\Support\WhatsAppConfig;
use Modules\CRM\Jobs\SendWhatsAppCampaignJob;
use Modules\CRM\Models\Contact;
use Modules\CRM\Models\ContactForm;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\WhatsAppCampaign;
use Modules\CRM\Models\WhatsAppMessageLog;
use Modules\CRM\Models\WhatsAppTemplate;

class WhatsAppCampaignService
{
    public function __construct(
        private readonly WhatsAppTemplateService $templateService,
    ) {}

    /**
     * @param  array<int|string, string>  $parameters
     * @param  array<string, mixed>  $recipientData
     */
    public function send(int $templateId, array $parameters, array $recipientData, int $userId): WhatsAppCampaign
    {
        if (! WhatsAppConfig::isConfigured()) {
            throw new \InvalidArgumentException(__('crm::whatsapp.messages.not_configured'));
        }

        $template = WhatsAppTemplate::query()->findOrFail($templateId);

        if (! $template->isSendable()) {
            throw new \InvalidArgumentException(__('crm::whatsapp.validation.template_not_sendable'));
        }

        $recipients = $this->resolveRecipients($recipientData);

        if ($recipients->isEmpty()) {
            throw new \InvalidArgumentException(__('crm::whatsapp.validation.no_recipients'));
        }

        $normalizedParameters = $this->normalizeParameters($parameters, $template);
        $renderedPreview = $this->templateService->renderPreview($template, $normalizedParameters);

        $campaign = WhatsAppCampaign::query()->create([
            'user_id' => $userId,
            'whatsapp_template_id' => $template->id,
            'template_parameters' => $normalizedParameters,
            'rendered_preview' => $renderedPreview,
            'recipients_count' => $recipients->count(),
            'status' => WhatsAppCampaign::STATUS_PENDING,
            'recipient_sources' => $this->buildRecipientSources($recipientData),
        ]);

        $now = now();
        WhatsAppMessageLog::query()->insert(
            $recipients->map(fn (array $recipient) => [
                'whatsapp_campaign_id' => $campaign->id,
                'phone' => $recipient['phone'],
                'recipient_type' => $recipient['type'],
                'recipient_id' => $recipient['id'],
                'status' => WhatsAppMessageLog::STATUS_PENDING,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all()
        );

        SendWhatsAppCampaignJob::dispatch($campaign->id, app()->getLocale());

        return $campaign;
    }

    /**
     * @param  array<string, mixed>  $recipientData
     * @return Collection<int, array{phone: string, type: string, id: ?int}>
     */
    public function resolveRecipients(array $recipientData): Collection
    {
        $recipients = collect();

        if (! empty($recipientData['all_leads'])) {
            Lead::query()
                ->where('blocked', false)
                ->whereNotNull('phone')
                ->select(['id', 'phone'])
                ->each(function (Lead $lead) use ($recipients) {
                    $recipients->push([
                        'phone' => $this->normalizePhone($lead->phone),
                        'type' => 'lead',
                        'id' => $lead->id,
                    ]);
                });
        } elseif (! empty($recipientData['lead_ids'])) {
            Lead::query()
                ->where('blocked', false)
                ->whereIn('id', $recipientData['lead_ids'])
                ->whereNotNull('phone')
                ->select(['id', 'phone'])
                ->each(function (Lead $lead) use ($recipients) {
                    $recipients->push([
                        'phone' => $this->normalizePhone($lead->phone),
                        'type' => 'lead',
                        'id' => $lead->id,
                    ]);
                });
        }

        if (! empty($recipientData['all_contacts'])) {
            Contact::query()
                ->where(function ($query) {
                    $query->whereNotNull('phone')->orWhereNotNull('phone2');
                })
                ->select(['id', 'phone', 'phone2'])
                ->each(function (Contact $contact) use ($recipients) {
                    foreach (array_filter([$contact->phone, $contact->phone2]) as $phone) {
                        $recipients->push([
                            'phone' => $this->normalizePhone($phone),
                            'type' => 'contact',
                            'id' => $contact->id,
                        ]);
                    }
                });
        } elseif (! empty($recipientData['contact_ids'])) {
            Contact::query()
                ->whereIn('id', $recipientData['contact_ids'])
                ->where(function ($query) {
                    $query->whereNotNull('phone')->orWhereNotNull('phone2');
                })
                ->select(['id', 'phone', 'phone2'])
                ->each(function (Contact $contact) use ($recipients) {
                    foreach (array_filter([$contact->phone, $contact->phone2]) as $phone) {
                        $recipients->push([
                            'phone' => $this->normalizePhone($phone),
                            'type' => 'contact',
                            'id' => $contact->id,
                        ]);
                    }
                });
        }

        if (! empty($recipientData['all_deals'])) {
            Deal::query()
                ->with(['lead:id,phone', 'company:id,phone'])
                ->select(['id', 'lead_id', 'company_id', 'title'])
                ->each(function (Deal $deal) use ($recipients) {
                    $phone = $deal->lead?->phone ?? $deal->company?->phone;
                    if ($phone) {
                        $recipients->push([
                            'phone' => $this->normalizePhone($phone),
                            'type' => 'deal',
                            'id' => $deal->id,
                        ]);
                    }
                });
        } elseif (! empty($recipientData['deal_ids'])) {
            Deal::query()
                ->whereIn('id', $recipientData['deal_ids'])
                ->with(['lead:id,phone', 'company:id,phone'])
                ->select(['id', 'lead_id', 'company_id', 'title'])
                ->each(function (Deal $deal) use ($recipients) {
                    $phone = $deal->lead?->phone ?? $deal->company?->phone;
                    if ($phone) {
                        $recipients->push([
                            'phone' => $this->normalizePhone($phone),
                            'type' => 'deal',
                            'id' => $deal->id,
                        ]);
                    }
                });
        }

        if (! empty($recipientData['all_contact_forms'])) {
            ContactForm::query()
                ->where('blocked', false)
                ->whereNotNull('mobile')
                ->select(['id', 'mobile'])
                ->each(function (ContactForm $form) use ($recipients) {
                    $recipients->push([
                        'phone' => $this->normalizePhone($form->mobile),
                        'type' => 'contact_form',
                        'id' => $form->id,
                    ]);
                });
        } elseif (! empty($recipientData['contact_form_ids'])) {
            ContactForm::query()
                ->where('blocked', false)
                ->whereIn('id', $recipientData['contact_form_ids'])
                ->whereNotNull('mobile')
                ->select(['id', 'mobile'])
                ->each(function (ContactForm $form) use ($recipients) {
                    $recipients->push([
                        'phone' => $this->normalizePhone($form->mobile),
                        'type' => 'contact_form',
                        'id' => $form->id,
                    ]);
                });
        }

        if (! empty($recipientData['custom_phones'])) {
            foreach ($this->parseCustomPhones($recipientData['custom_phones']) as $phone) {
                $recipients->push([
                    'phone' => $phone,
                    'type' => 'custom',
                    'id' => null,
                ]);
            }
        }

        return $recipients
            ->filter(fn (array $r) => $this->isValidPhone($r['phone']))
            ->unique('phone')
            ->values();
    }

    /**
     * @param  string|array<int, string|array{value?: string}>|null  $raw
     * @return array<int, string>
     */
    public function parseCustomPhones(string|array|null $raw): array
    {
        if (empty($raw)) {
            return [];
        }

        if (is_array($raw)) {
            return collect($raw)
                ->map(function ($item) {
                    if (is_array($item)) {
                        return $this->normalizePhone((string) ($item['value'] ?? ''));
                    }

                    return $this->normalizePhone((string) $item);
                })
                ->filter(fn (string $phone) => $phone !== '')
                ->all();
        }

        $raw = trim($raw);

        if (str_starts_with($raw, '[')) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $this->parseCustomPhones($decoded);
            }
        }

        return collect(preg_split('/[\s,;]+/', $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [])
            ->map(fn (string $phone) => $this->normalizePhone($phone))
            ->filter(fn (string $phone) => $phone !== '')
            ->all();
    }

    public function normalizePhone(string $phone): string
    {
        $phone = trim($phone);
        $hasPlus = str_starts_with($phone, '+');
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return '';
        }

        return $hasPlus ? '+'.$digits : $digits;
    }

    private function isValidPhone(string $phone): bool
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        return strlen($digits) >= 8 && strlen($digits) <= 15;
    }

    /**
     * @param  array<int|string, string>  $parameters
     * @return array<int, string>
     */
    private function normalizeParameters(array $parameters, WhatsAppTemplate $template): array
    {
        $variables = $this->templateService->parseBodyVariables($template->body);
        $normalized = [];

        foreach ($variables as $index) {
            $normalized[$index] = trim((string) ($parameters[$index] ?? $parameters[(string) $index] ?? ''));
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $recipientData
     * @return array<string, mixed>
     */
    private function buildRecipientSources(array $recipientData): array
    {
        return [
            'all_leads' => (bool) ($recipientData['all_leads'] ?? false),
            'lead_ids' => array_values($recipientData['lead_ids'] ?? []),
            'all_contacts' => (bool) ($recipientData['all_contacts'] ?? false),
            'contact_ids' => array_values($recipientData['contact_ids'] ?? []),
            'all_deals' => (bool) ($recipientData['all_deals'] ?? false),
            'deal_ids' => array_values($recipientData['deal_ids'] ?? []),
            'all_contact_forms' => (bool) ($recipientData['all_contact_forms'] ?? false),
            'contact_form_ids' => array_values($recipientData['contact_form_ids'] ?? []),
            'custom_phones' => $this->parseCustomPhones($recipientData['custom_phones'] ?? null),
        ];
    }
}

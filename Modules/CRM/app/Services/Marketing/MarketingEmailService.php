<?php

namespace Modules\CRM\Services\Marketing;

use Illuminate\Support\Collection;
use Modules\CRM\Jobs\SendMarketingCampaignJob;
use Modules\CRM\Models\Contact;
use Modules\CRM\Models\ContactForm;
use Modules\CRM\Models\MarketingCampaign;
use Modules\Support\Models\Subscriber;
use Illuminate\Support\Str;

class MarketingEmailService
{
    /**
     * @param  array{
     *     all_subscribers?: bool,
     *     subscriber_ids?: array<int>,
     *     all_contacts?: bool,
     *     contact_ids?: array<int>,
     *     all_contact_forms?: bool,
     *     contact_form_ids?: array<int>,
     *     custom_emails?: string|null,
     * }  $recipientData
     */
    public function send(string $subject, string $body, array $recipientData, int $userId): MarketingCampaign
    {
        $recipients = $this->resolveRecipients($recipientData);

        if ($recipients->isEmpty()) {
            throw new \InvalidArgumentException(__('crm::marketing.validation.no_recipients'));
        }

        $campaign = MarketingCampaign::query()->create([
            'user_id' => $userId,
            'subject' => $subject,
            'body' => $body,
            'recipients_count' => $recipients->count(),
            'status' => MarketingCampaign::STATUS_PENDING,
            'recipient_sources' => $this->buildRecipientSources($recipientData),
        ]);

        SendMarketingCampaignJob::dispatch(
            $campaign->id,
            $recipients->all(),
            app()->getLocale(),
        );

        return $campaign;
    }

    /**
     * @param  array{
     *     all_subscribers?: bool,
     *     subscriber_ids?: array<int>,
     *     all_contacts?: bool,
     *     contact_ids?: array<int>,
     *     all_contact_forms?: bool,
     *     contact_form_ids?: array<int>,
     *     custom_emails?: string|null,
     * }  $recipientData
     */
    public function resolveRecipients(array $recipientData): Collection
    {
        $emails = collect();

        if (! empty($recipientData['all_subscribers'])) {
            $emails = $emails->merge(
                Subscriber::query()
                    ->where('blocked', false)
                    ->whereNotNull('email')
                    ->pluck('email')
            );
        } elseif (! empty($recipientData['subscriber_ids'])) {
            $emails = $emails->merge(
                Subscriber::query()
                    ->where('blocked', false)
                    ->whereIn('id', $recipientData['subscriber_ids'])
                    ->whereNotNull('email')
                    ->pluck('email')
            );
        }

        if (! empty($recipientData['all_contacts'])) {
            $emails = $emails->merge(
                Contact::query()
                    ->whereNotNull('email')
                    ->pluck('email')
            );
        } elseif (! empty($recipientData['contact_ids'])) {
            $emails = $emails->merge(
                Contact::query()
                    ->whereIn('id', $recipientData['contact_ids'])
                    ->whereNotNull('email')
                    ->pluck('email')
            );
        }

        if (! empty($recipientData['all_contact_forms'])) {
            $emails = $emails->merge(
                ContactForm::query()
                    ->where('blocked', false)
                    ->whereNotNull('email')
                    ->pluck('email')
            );
        } elseif (! empty($recipientData['contact_form_ids'])) {
            $emails = $emails->merge(
                ContactForm::query()
                    ->where('blocked', false)
                    ->whereIn('id', $recipientData['contact_form_ids'])
                    ->whereNotNull('email')
                    ->pluck('email')
            );
        }

        if (! empty($recipientData['custom_emails'])) {
            $emails = $emails->merge($this->parseCustomEmails($recipientData['custom_emails']));
        }

        return $emails
            ->map(fn (string $email) => Str::lower(trim($email)))
            ->filter(fn (string $email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values();
    }

    /**
     * @param  string|array<int, string|array{value?: string, email?: string}>|null  $raw
     */
    public function parseCustomEmails(string|array|null $raw): Collection
    {
        if (empty($raw)) {
            return collect();
        }

        if (is_array($raw)) {
            return collect($raw)->map(function ($item) {
                if (is_array($item)) {
                    return trim((string) ($item['value'] ?? $item['email'] ?? ''));
                }

                return trim((string) $item);
            });
        }

        $raw = trim($raw);

        if (str_starts_with($raw, '[')) {
            $decoded = json_decode($raw, true);

            if (is_array($decoded)) {
                return $this->parseCustomEmails($decoded);
            }
        }

        return collect(preg_split('/[\s,;]+/', $raw, -1, PREG_SPLIT_NO_EMPTY) ?: []);
    }

    /**
     * @return array<int, string>
     */
    public function normalizedCustomEmails(mixed $raw): array
    {
        return $this->parseCustomEmails(is_string($raw) || is_array($raw) ? $raw : null)
            ->map(fn (string $email) => Str::lower(trim($email)))
            ->filter(fn (string $email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $recipientData
     * @return array<string, mixed>
     */
    private function buildRecipientSources(array $recipientData): array
    {
        return [
            'all_subscribers' => (bool) ($recipientData['all_subscribers'] ?? false),
            'subscriber_ids' => array_values($recipientData['subscriber_ids'] ?? []),
            'all_contacts' => (bool) ($recipientData['all_contacts'] ?? false),
            'contact_ids' => array_values($recipientData['contact_ids'] ?? []),
            'all_contact_forms' => (bool) ($recipientData['all_contact_forms'] ?? false),
            'contact_form_ids' => array_values($recipientData['contact_form_ids'] ?? []),
            'custom_emails' => $this->normalizedCustomEmails($recipientData['custom_emails'] ?? null),
        ];
    }
}

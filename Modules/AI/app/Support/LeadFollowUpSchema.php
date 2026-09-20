<?php

namespace Modules\AI\Support;

use Carbon\Carbon;
use Modules\CRM\Models\CrmActivity;
use Throwable;

class LeadFollowUpSchema
{
    /**
     * @return array<string, string>
     */
    public static function fieldGuides(): array
    {
        $types = implode(', ', CrmActivity::TYPES);

        return [
            'type' => 'Exactly one of: '.$types.'. Use task for a next-step reminder, call for a phone conversation, email for a written message, meeting if a call/meeting should be booked, note only for internal context with no outbound action.',
            'title' => 'Short follow-up title, max 80 characters. Name the next action and the reason.',
            'body' => 'Ready-to-use follow-up. For email, write the message. For call or meeting, write talking points. For task or note, write the next step. Use only facts from the lead, timeline, and project activity. Do not invent quotes, dates, or project status.',
            'scheduled_at' => 'Future local datetime as YYYY-MM-DDTHH:MM (datetime-local). Usually 1-3 business days from now, earlier if the lead is hot or a project is overdue.',
        ];
    }

    public static function systemPrompt(string $locale): string
    {
        $prompt = 'You create one CRM follow-up activity for a sales lead as a single JSON object. '
            .'Write human-readable copy in locale "'.$locale.'". '
            .'Return ONLY valid JSON with exactly these keys and purposes:'."\n"
            .json_encode(self::fieldGuides(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n"
            .'Base the follow-up on the lead record, recent CRM activity, and related project activity. '
            .'Do not invent clients, project progress, or promises that are not in the context. '
            .'Do not claim a message was already sent. Do not wrap the JSON in markdown fences.';

        $profile = CompanyContentProfile::promptBlock();

        if ($profile !== '') {
            $prompt .= "\n\n".$profile;
        }

        return $prompt;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public static function userMessage(array $context, ?string $instruction = null): string
    {
        $message = 'Create the next follow-up activity for this lead.'
            ."\nCurrent datetime: ".now()->format('Y-m-d H:i')
            ."\n\nLead, activity, and project context:\n"
            .json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        $instruction = is_string($instruction) ? trim($instruction) : '';

        if ($instruction !== '') {
            $message .= "\n\nExtra instruction from the salesperson:\n".$instruction;
        }

        return $message;
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array{type: string, title: string, body: string, scheduled_at: string}
     */
    public static function normalize(array $raw): array
    {
        return [
            'type' => self::normalizeType($raw['type'] ?? null),
            'title' => self::limit(self::scalar($raw['title'] ?? ''), 255),
            'body' => self::limit(self::scalar($raw['body'] ?? ''), 4000),
            'scheduled_at' => self::normalizeScheduledAt($raw['scheduled_at'] ?? null),
        ];
    }

    public static function normalizeType(mixed $value): string
    {
        $type = strtolower(self::scalar($value));

        $aliases = [
            'follow_up' => CrmActivity::TYPE_TASK,
            'followup' => CrmActivity::TYPE_TASK,
            'follow-up' => CrmActivity::TYPE_TASK,
            'phone' => CrmActivity::TYPE_CALL,
            'phone_call' => CrmActivity::TYPE_CALL,
            'mail' => CrmActivity::TYPE_EMAIL,
            'e-mail' => CrmActivity::TYPE_EMAIL,
        ];

        $type = $aliases[$type] ?? $type;

        if (! in_array($type, CrmActivity::TYPES, true)) {
            return CrmActivity::TYPE_TASK;
        }

        return $type;
    }

    public static function normalizeScheduledAt(mixed $value): string
    {
        $fallback = now()->addDay()->setTime(10, 0);

        try {
            $parsed = is_string($value) && trim($value) !== ''
                ? Carbon::parse(trim($value))
                : $fallback;
        } catch (Throwable) {
            $parsed = $fallback;
        }

        if ($parsed->lessThanOrEqualTo(now())) {
            $parsed = $fallback;
        }

        return $parsed->format('Y-m-d\TH:i');
    }

    private static function scalar(mixed $value): string
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        if (! is_scalar($value)) {
            return '';
        }

        return trim((string) $value);
    }

    private static function limit(string $value, int $max): string
    {
        if (mb_strlen($value) <= $max) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $max));
    }
}

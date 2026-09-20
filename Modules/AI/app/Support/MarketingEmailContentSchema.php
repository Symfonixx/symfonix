<?php

namespace Modules\AI\Support;

use Modules\CRM\Models\MarketingGroup;

class MarketingEmailContentSchema
{
    /**
     * @return array<string, string>
     */
    public static function fieldGuides(): array
    {
        return [
            'subject' => 'Plain-text email subject line, max 90 characters, no HTML. Specific to the campaign title and goal. No clickbait.',
            'body' => 'Complete marketing email as a semantic HTML fragment using <p>, <h2>, <h3>, <ul>, <li>, <strong>, and <em>. 2-5 short paragraphs plus a clear call to action. No markdown, no code fences, no <html> or <body>. Match the company voice. Do not invent offers, prices, or deadlines that are not in the campaign goal or extra instructions.',
        ];
    }

    public static function systemPrompt(string $locale): string
    {
        $prompt = 'You write one marketing email for a campaign as a single JSON object. '
            .'Write human-readable copy in locale "'.$locale.'". '
            .'Return ONLY valid JSON with exactly these keys and purposes:'."\n"
            .json_encode(self::fieldGuides(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n"
            .'The email must serve the campaign goal. Stay consistent with the company profile. '
            .'Do not wrap the JSON in markdown fences.';

        $profile = CompanyContentProfile::promptBlock();

        if ($profile !== '') {
            $prompt .= "\n\n".$profile;
        }

        return $prompt;
    }

    /**
     * @param  array{title: string, goal: string}  $campaign
     */
    public static function userMessage(array $campaign, ?string $instruction = null): string
    {
        $message = 'Write the marketing email for this campaign.'
            ."\n\nCampaign:\n"
            .json_encode($campaign, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        $instruction = is_string($instruction) ? trim($instruction) : '';

        if ($instruction !== '') {
            $message .= "\n\nExtra instruction from the marketer:\n".$instruction;
        }

        return $message;
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array{subject: string, body: string}
     */
    public static function normalize(array $raw): array
    {
        return [
            'subject' => self::limit(strip_tags(self::scalar($raw['subject'] ?? '')), 255),
            'body' => self::limit(self::scalar($raw['body'] ?? ''), 50000),
        ];
    }

    /**
     * @return array{title: string, goal: string}
     */
    public static function campaignContext(?MarketingGroup $group, ?string $title, ?string $goal): array
    {
        return [
            'title' => trim($title ?: (string) ($group?->title ?? '')),
            'goal' => trim($goal ?: (string) ($group?->goal ?? '')),
        ];
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

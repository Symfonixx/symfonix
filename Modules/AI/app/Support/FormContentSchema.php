<?php

namespace Modules\AI\Support;

use Illuminate\Support\Str;

class FormContentSchema
{
    /**
     * @return list<string>
     */
    public static function types(): array
    {
        return array_keys(self::definitions());
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function get(string $type): ?array
    {
        return self::definitions()[$type] ?? null;
    }

    /**
     * @return array<string, array{label: string, fields: array<string, array{type: string, max?: int, guide: string}>}>
     */
    public static function definitions(): array
    {
        $cmsFields = [
            'title' => [
                'type' => 'text',
                'max' => 120,
                'guide' => 'Compelling title, max 70 characters, no clickbait.',
            ],
            'slug' => [
                'type' => 'slug',
                'max' => 160,
                'guide' => 'English lowercase hyphenated URL slug. No spaces or special characters.',
            ],
            'description' => [
                'type' => 'text',
                'max' => 160,
                'guide' => 'SEO meta description, 150-160 characters.',
            ],
            'keywords' => [
                'type' => 'keywords',
                'max' => 255,
                'guide' => '5-10 relevant SEO keywords, comma-separated.',
            ],
            'content' => [
                'type' => 'html',
                'guide' => 'Full body as a semantic HTML fragment using <p>, <h2>, <h3>, <ul>, <li>, <strong>, and <em>. No markdown, no code fences, no <html> or <body>.',
            ],
        ];

        return [
            'cms_blog' => [
                'label' => 'blog post',
                'fields' => array_replace($cmsFields, [
                    'content' => [
                        'type' => 'html',
                        'guide' => 'A complete blog article (4-8 sections) as a semantic HTML fragment using <p>, <h2>, <h3>, <ul>, <li>, <strong>, and <em>. No markdown, no code fences, no <html> or <body>.',
                    ],
                ]),
            ],
            'cms_page' => [
                'label' => 'website page',
                'fields' => array_replace($cmsFields, [
                    'content' => [
                        'type' => 'html',
                        'guide' => 'A complete landing/about-style page (3-6 sections) as a semantic HTML fragment using <p>, <h2>, <h3>, <ul>, <li>, <strong>, and <em>. No markdown, no code fences, no <html> or <body>.',
                    ],
                ]),
            ],
            'service' => [
                'label' => 'service offering',
                'fields' => array_replace($cmsFields, [
                    'content' => [
                        'type' => 'html',
                        'guide' => 'A service page covering benefits, process, and who it is for, as a semantic HTML fragment using <p>, <h2>, <h3>, <ul>, <li>, <strong>, and <em>. No markdown, no code fences, no <html> or <body>.',
                    ],
                ]),
            ],
            'product' => [
                'label' => 'public website product page',
                'fields' => [
                    'name' => [
                        'type' => 'text',
                        'max' => 120,
                        'guide' => 'Product name shown on the public website, clear and specific, max 70 characters.',
                    ],
                    'short_description' => [
                        'type' => 'text',
                        'max' => 500,
                        'guide' => 'Website teaser shown on the catalog card, 1-3 sentences, max 500 characters.',
                    ],
                    'description' => [
                        'type' => 'html',
                        'guide' => 'Full public product-page description as a semantic HTML fragment using <p>, <h2>, <h3>, <ul>, <li>, <strong>, and <em>. Cover benefits, features, and who it is for. No markdown, no code fences, no <html> or <body>.',
                    ],
                    'seo_title' => [
                        'type' => 'text',
                        'max' => 70,
                        'guide' => 'Website SEO title, max 60 characters.',
                    ],
                    'seo_description' => [
                        'type' => 'text',
                        'max' => 160,
                        'guide' => 'Website SEO meta description, 150-160 characters.',
                    ],
                    'seo_keywords' => [
                        'type' => 'keywords',
                        'max' => 255,
                        'guide' => '5-10 relevant website SEO keywords, comma-separated.',
                    ],
                ],
            ],
            'use_case' => [
                'label' => 'project use case / case study',
                'fields' => [
                    'title' => [
                        'type' => 'text',
                        'max' => 120,
                        'guide' => 'Case-study title, max 70 characters.',
                    ],
                    'slug' => [
                        'type' => 'slug',
                        'max' => 160,
                        'guide' => 'English lowercase hyphenated URL slug. No spaces or special characters.',
                    ],
                    'client_name' => [
                        'type' => 'text',
                        'max' => 120,
                        'guide' => 'Client or company name. Use a realistic generic name if none is given; do not invent famous brands that contradict the company profile.',
                    ],
                    'summary' => [
                        'type' => 'text',
                        'max' => 400,
                        'guide' => 'One-paragraph project summary.',
                    ],
                    'challenge' => [
                        'type' => 'text',
                        'max' => 800,
                        'guide' => 'The client problem or challenge.',
                    ],
                    'solution' => [
                        'type' => 'text',
                        'max' => 800,
                        'guide' => 'How the work was delivered.',
                    ],
                    'results' => [
                        'type' => 'text',
                        'max' => 800,
                        'guide' => 'Outcomes. Do not invent metrics that contradict the company profile.',
                    ],
                    'content' => [
                        'type' => 'html',
                        'guide' => 'Longer case-study body as a semantic HTML fragment using <p>, <h2>, <h3>, <ul>, <li>, <strong>, and <em>. No markdown, no code fences, no <html> or <body>.',
                    ],
                    'technologies' => [
                        'type' => 'keywords',
                        'max' => 255,
                        'guide' => 'Comma-separated technologies or tools used.',
                    ],
                    'category_tag' => [
                        'type' => 'text',
                        'max' => 80,
                        'guide' => 'Short category label such as Web Development.',
                    ],
                ],
            ],
        ];
    }

    public static function systemPrompt(string $type, string $locale): string
    {
        $definition = self::get($type);

        if ($definition === null) {
            return '';
        }

        $guides = [];
        foreach ($definition['fields'] as $key => $field) {
            $guides[$key] = $field['guide'];
        }

        $prompt = 'You generate complete admin-form content as a single JSON object. '
            .'Write the human-readable copy in locale "'.$locale.'". '
            .'The slug (if requested) must always be English lowercase hyphenated text. '
            .'The form is a '.$definition['label'].'. '
            .'Return ONLY valid JSON with exactly these keys and purposes:'."\n"
            .json_encode($guides, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n"
            .'Match the company voice and positioning from the company profile below. '
            .'Do not invent awards, clients, or claims that contradict that profile. '
            .'Do not wrap the JSON in markdown fences.';

        $profile = CompanyContentProfile::promptBlock();

        if ($profile !== '') {
            $prompt .= "\n\n".$profile;
        }

        return $prompt;
    }

    /**
     * @param  array<string, string>|null  $existing
     */
    public static function userMessage(string $prompt, ?array $existing = null): string
    {
        $message = 'Topic / brief:'."\n".$prompt;

        if ($existing === []) {
            $existing = null;
        }

        if (is_array($existing)) {
            $filtered = [];
            foreach ($existing as $key => $value) {
                if (! is_string($key) || ! is_string($value) || trim($value) === '') {
                    continue;
                }

                $filtered[$key] = trim($value);
            }

            if ($filtered !== []) {
                $message .= "\n\nExisting form values to respect or improve:\n"
                    .json_encode($filtered, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }

        return $message;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function decode(string $content): ?array
    {
        $content = trim($content);
        $content = preg_replace('/^```(?:json)?\s*/i', '', $content) ?? $content;
        $content = preg_replace('/```\s*$/', '', $content) ?? $content;
        $content = trim($content);

        $decoded = json_decode($content, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{.*\}/s', $content, $matches) === 1) {
            $decoded = json_decode($matches[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, string>
     */
    public static function normalize(string $type, array $raw): array
    {
        $definition = self::get($type);

        if ($definition === null) {
            return [];
        }

        $fields = [];

        foreach ($definition['fields'] as $key => $meta) {
            $fields[$key] = self::normalizeField($raw[$key] ?? '', $meta);
        }

        return $fields;
    }

    /**
     * @param  array{type: string, max?: int, guide: string}  $meta
     */
    private static function normalizeField(mixed $value, array $meta): string
    {
        if (is_array($value)) {
            $parts = [];
            foreach ($value as $item) {
                if (is_array($item)) {
                    $item = $item['value'] ?? $item['name'] ?? reset($item);
                }

                if (is_scalar($item) && trim((string) $item) !== '') {
                    $parts[] = trim((string) $item);
                }
            }
            $value = implode(', ', $parts);
        }

        if (! is_scalar($value)) {
            return '';
        }

        $value = trim((string) $value);
        $type = $meta['type'];

        if ($type === 'html') {
            $value = self::normalizeHtml($value);
        }

        if ($type === 'slug') {
            $value = Str::slug($value);
        }

        if ($type === 'keywords') {
            $keywords = array_values(array_filter(array_map(
                static fn (string $keyword): string => trim($keyword),
                preg_split('/\s*,\s*/', $value) ?: [],
            )));
            $value = implode(', ', $keywords);
        }

        if (isset($meta['max']) && mb_strlen($value) > $meta['max']) {
            $value = rtrim(mb_substr($value, 0, $meta['max']));
        }

        return $value;
    }

    private static function normalizeHtml(string $content): string
    {
        $content = trim($content);
        $content = preg_replace('/^```(?:html|json)?\s*/i', '', $content) ?? $content;
        $content = preg_replace('/```\s*$/', '', $content) ?? $content;

        return trim($content);
    }
}

<?php

namespace Modules\AI\Support;

use Illuminate\Support\Str;

class FormContentSchema
{
    public const MODE_CREATE = 'create';

    public const MODE_OPTIMIZE = 'optimize';

    private const HTML_FRAGMENT = ' as a semantic HTML fragment using <p>, <h2>, <h3>, <ul>, <li>, <strong>, and <em>. No markdown, no code fences, no <html> or <body>.';

    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    /**
     * @return list<string>
     */
    public static function types(): array
    {
        return array_keys(self::definitions());
    }

    /**
     * @return list<string>
     */
    public static function modes(): array
    {
        return [self::MODE_CREATE, self::MODE_OPTIMIZE];
    }

    public static function normalizeMode(?string $mode): string
    {
        return $mode === self::MODE_OPTIMIZE ? self::MODE_OPTIMIZE : self::MODE_CREATE;
    }

    /**
     * Catalog permissions that may generate or optimize this form type.
     *
     * @return list<string>
     */
    public static function permissions(string $type): array
    {
        return match ($type) {
            'cms_blog' => ['cms.blogs.create', 'cms.blogs.edit'],
            'cms_page' => ['cms.pages.create', 'cms.pages.edit'],
            'service' => ['services.catalog.create', 'services.catalog.edit'],
            'product' => ['product.catalog.create', 'product.catalog.edit'],
            'use_case' => ['project.use_cases.create', 'project.use_cases.edit'],
            default => [],
        };
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
        ];

        return [
            'cms_blog' => [
                'label' => 'blog post',
                'fields' => $cmsFields + [
                    'content' => self::htmlField('A complete blog article (4-8 sections)'),
                ],
            ],
            'cms_page' => [
                'label' => 'website page',
                'fields' => $cmsFields + [
                    'content' => self::htmlField('A complete landing/about-style page (3-6 sections)'),
                ],
            ],
            'service' => [
                'label' => 'service offering',
                'fields' => $cmsFields + [
                    'content' => self::htmlField('A service page covering benefits, process, and who it is for'),
                ],
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
                    'description' => self::htmlField(
                        'Full public product-page description',
                        'Cover benefits, features, and who it is for.',
                    ),
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
                    'content' => self::htmlField('Longer case-study body'),
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

    public static function systemPrompt(string $type, string $locale, string $mode = self::MODE_CREATE): string
    {
        $definition = self::get($type);

        if ($definition === null) {
            return '';
        }

        $guides = json_encode(self::fieldGuides($definition), JSON_PRETTY_PRINT | self::JSON_FLAGS);
        $label = $definition['label'];
        $mode = self::normalizeMode($mode);

        $prompt = $mode === self::MODE_OPTIMIZE
            ? 'You optimize existing admin-form content as a single JSON object. '
                .'Rewrite every field so it is clearer, more professional, better structured, and more SEO-friendly. '
                .'Keep the original meaning, facts, names, numbers, and claims. '
                .'Do not invent new clients, awards, metrics, features, or results. '
                .'Keep the same language/locale "'.$locale.'". '
                .'Keep the slug unchanged unless it is clearly invalid. '
                .'The form is a '.$label.'. '
                .'Return ONLY valid JSON with exactly these keys and purposes:'."\n"
                .$guides."\n"
                .'Match the company voice and positioning from the company profile below. '
                .'Do not wrap the JSON in markdown fences.'
            : 'You generate complete admin-form content as a single JSON object. '
                .'Write the human-readable copy in locale "'.$locale.'". '
                .'The slug (if requested) must always be English lowercase hyphenated text. '
                .'The form is a '.$label.'. '
                .'Return ONLY valid JSON with exactly these keys and purposes:'."\n"
                .$guides."\n"
                .'Match the company voice and positioning from the company profile below. '
                .'Do not invent awards, clients, or claims that contradict that profile. '
                .'Do not wrap the JSON in markdown fences.';

        return CompanyContentProfile::appendTo($prompt);
    }

    /**
     * @param  array<string, mixed>|null  $existing
     */
    public static function userMessage(string $prompt, ?array $existing = null, string $mode = self::MODE_CREATE): string
    {
        $filtered = self::filledExisting($existing);
        $mode = self::normalizeMode($mode);

        if ($mode === self::MODE_OPTIMIZE) {
            $message = 'Optimize this existing form content. Improve SEO, readability, structure, and conversion without changing the facts.';

            if (trim($prompt) !== '') {
                $message .= "\n\nExtra instructions:\n".$prompt;
            }

            if ($filtered !== []) {
                $message .= "\n\nCurrent field values:\n"
                    .json_encode($filtered, self::JSON_FLAGS);
            }

            return $message;
        }

        $message = 'Topic / brief:'."\n".$prompt;

        if ($filtered !== []) {
            $message .= "\n\nExisting form values to respect or improve:\n"
                .json_encode($filtered, self::JSON_FLAGS);
        }

        return $message;
    }

    /**
     * @param  array<array-key, mixed>|null  $existing
     * @return array<string, string>
     */
    public static function filledExisting(?array $existing): array
    {
        if (! is_array($existing)) {
            return [];
        }

        $filtered = [];

        foreach ($existing as $key => $value) {
            if (! is_string($key) || ! is_string($value) || trim($value) === '') {
                continue;
            }

            $filtered[$key] = trim($value);
        }

        return $filtered;
    }

    /**
     * @param  array<string, string>  $fields
     */
    public static function hasContent(array $fields): bool
    {
        foreach ($fields as $value) {
            if ($value !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function decode(string $content): ?array
    {
        $content = self::stripFences($content, 'json');

        $decoded = json_decode($content, true);
        if (is_array($decoded)) {
            return self::unwrapDecoded($decoded);
        }

        if (preg_match('/\{.*\}/s', $content, $matches) === 1) {
            $decoded = json_decode($matches[0], true);
            if (is_array($decoded)) {
                return self::unwrapDecoded($decoded);
            }
        }

        return null;
    }

    /**
     * @param  array<array-key, mixed>  $decoded
     * @return array<array-key, mixed>
     */
    private static function unwrapDecoded(array $decoded): array
    {
        if ($decoded !== [] && array_is_list($decoded)) {
            $first = $decoded[0] ?? null;
            if (is_array($first) && $first !== [] && ! array_is_list($first)) {
                $decoded = $first;
            }
        }

        if (self::looksLikeFormFields($decoded)) {
            return $decoded;
        }

        foreach (['fields', 'data', 'result'] as $wrapper) {
            $nested = $decoded[$wrapper] ?? null;
            if (is_array($nested) && self::looksLikeFormFields($nested)) {
                return $nested;
            }
        }

        return $decoded;
    }

    /**
     * @param  array<array-key, mixed>  $decoded
     */
    private static function looksLikeFormFields(array $decoded): bool
    {
        if ($decoded === [] || array_is_list($decoded)) {
            return false;
        }

        foreach (['title', 'name', 'slug', 'description', 'short_description', 'content', 'keywords', 'seo_title'] as $key) {
            if (array_key_exists($key, $decoded)) {
                return true;
            }
        }

        return false;
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
     * @param  array{label: string, fields: array<string, array{type: string, max?: int, guide: string}>}  $definition
     * @return array<string, string>
     */
    private static function fieldGuides(array $definition): array
    {
        $guides = [];

        foreach ($definition['fields'] as $key => $field) {
            $guides[$key] = $field['guide'];
        }

        return $guides;
    }

    /**
     * @return array{type: string, guide: string}
     */
    private static function htmlField(string $purpose, string $extra = ''): array
    {
        $guide = $purpose.self::HTML_FRAGMENT;

        if ($extra !== '') {
            $guide .= ' '.$extra;
        }

        return [
            'type' => 'html',
            'guide' => $guide,
        ];
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
            $value = self::stripFences($value, 'html|json');
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

    public static function stripFences(string $content, string $languages = 'html|json'): string
    {
        $content = trim($content);
        $content = preg_replace('/^```(?:'.$languages.')?\s*/i', '', $content) ?? $content;
        $content = preg_replace('/```\s*$/', '', $content) ?? $content;

        return trim($content);
    }
}

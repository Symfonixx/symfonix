<?php

namespace Modules\AI\Support;

use Illuminate\Support\Facades\Storage;
use Modules\Base\Models\Seo;
use Modules\Base\Models\Settings;
use Modules\Base\Support\CompanyBranding;

class CompanyContentProfile
{
    /**
     * @var array{name: string, binary: string, mime_type: string, url: string}|null
     */
    private static ?array $logoCache = null;

    private static bool $logoResolved = false;

    /**
     * Site SEO and contact facts used so generated copy matches the company.
     */
    public static function promptBlock(): string
    {
        $lines = [];

        foreach (self::facts() as $label => $value) {
            if ($value === '') {
                continue;
            }

            $lines[] = $label.': '.$value;
        }

        if ($lines === []) {
            return '';
        }

        return "Company profile (write in this brand's voice and stay consistent with these facts):\n"
            .implode("\n", $lines);
    }

    public static function appendTo(string $prompt): string
    {
        $profile = self::promptBlock();

        if ($profile === '') {
            return $prompt;
        }

        return $prompt."\n\n".$profile;
    }

    /**
     * @return array<string, string>
     */
    public static function facts(): array
    {
        return [
            'Company name' => self::seo('website_name', (string) config('app.name', 'Symfonix')),
            'Main title' => self::seo('main_title'),
            'About' => self::seo('about_us'),
            'SEO description' => self::seo('website_desc'),
            'Keywords / topics' => self::seo('website_keywords'),
            'Phone' => self::setting('phone'),
            'Email' => self::setting('email'),
            'Address' => self::setting('address'),
        ];
    }

    /**
     * Site logo from Settings > Branding, ready to send as a Gemini inline image.
     *
     * @return array{name: string, binary: string, mime_type: string, url: string}|null
     */
    public static function logoReference(): ?array
    {
        if (self::$logoResolved) {
            return self::$logoCache;
        }

        self::$logoResolved = true;

        $branding = CompanyBranding::forInvoice();
        $relative = $branding['logo'] ?? null;

        if (! is_string($relative) || $relative === '' || $relative === 'default.jpg') {
            return self::$logoCache = null;
        }

        $binary = null;
        $mimeType = 'image/png';
        $absolute = $branding['logo_path'] ?? null;

        if (is_string($absolute) && is_file($absolute)) {
            $binary = file_get_contents($absolute) ?: null;
            $detected = mime_content_type($absolute);
            $mimeType = is_string($detected) && $detected !== '' ? $detected : 'image/png';
        } elseif (Storage::disk('public')->exists($relative)) {
            $binary = Storage::disk('public')->get($relative);
            $mimeType = Storage::disk('public')->mimeType($relative) ?: 'image/png';
        }

        if (! is_string($binary) || $binary === '') {
            return self::$logoCache = null;
        }

        $allowed = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp', 'image/gif'];
        if (! in_array($mimeType, $allowed, true)) {
            return self::$logoCache = null;
        }

        $name = $branding['name'] ?? '';
        if (! is_string($name) || $name === '') {
            $name = (string) config('app.name', 'Symfonix');
        }

        return self::$logoCache = [
            'name' => $name,
            'binary' => $binary,
            'mime_type' => $mimeType,
            'url' => is_string($branding['logo_url'] ?? null) ? $branding['logo_url'] : asset('storage/'.$relative),
        ];
    }

    private static function seo(string $key, string $default = ''): string
    {
        try {
            return self::stringify(Seo::get($key, $default), $default);
        } catch (\Throwable) {
            return $default;
        }
    }

    private static function setting(string $key): string
    {
        try {
            return self::stringify(Settings::get($key), '');
        } catch (\Throwable) {
            return '';
        }
    }

    private static function stringify(mixed $value, string $default = ''): string
    {
        if (is_array($value)) {
            $locale = app()->getLocale();
            $value = $value[$locale] ?? reset($value) ?: $default;
        }

        if (! is_string($value) || trim($value) === '' || $value === 'false') {
            return $default;
        }

        $text = trim(preg_replace('/\s+/', ' ', strip_tags($value)) ?? '');

        return mb_strlen($text) > 500 ? mb_substr($text, 0, 497).'...' : $text;
    }
}

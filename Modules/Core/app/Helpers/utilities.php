<?php

use Illuminate\Support\Facades\Log;
use Modules\Core\Services\GoogleTranslationService;
use Stichoza\GoogleTranslate\Exceptions\LargeTextException;
use Stichoza\GoogleTranslate\Exceptions\RateLimitException;
use Stichoza\GoogleTranslate\Exceptions\TranslationRequestException;

if (! function_exists('autoGoogleTranslator')) {
    /**
     * @throws LargeTextException
     * @throws RateLimitException
     * @throws TranslationRequestException
     */
    function autoGoogleTranslator(string $targetLang, string $content): string
    {
        return app(GoogleTranslationService::class)->translate($targetLang, $content);
    }

}

if (! function_exists('otherLangs')) {
    function otherLangs()
    {
        $locale = app()->getLocale();

        return array_keys(array_filter(
            config('laravellocalization.supportedLocales'),
            function ($value, $key) use ($locale) {
                return $key != $locale;
            },
            ARRAY_FILTER_USE_BOTH
        ));
    }

}

if (! function_exists('wantsAutoTranslate')) {
    /**
     * Whether the current request opted into auto-translating other locales.
     */
    function wantsAutoTranslate(array|bool|null $source = null): bool
    {
        if (is_bool($source)) {
            return $source;
        }

        $value = is_array($source)
            ? ($source['auto_translate'] ?? false)
            : request()->boolean('auto_translate');

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}

if (! function_exists('buildFieldTranslations')) {
    /**
     * Build locale => value map for one translatable field.
     *
     * @param  array<string, string>|null  $existing
     * @return array<string, string>
     */
    function buildFieldTranslations(?string $value, bool $autoTranslate, ?array $existing = null): array
    {
        $locale = app()->getLocale();
        $value = $value ?? '';

        if ($autoTranslate) {
            $translations = [$locale => $value];

            foreach (otherLangs() as $lang) {
                if ($value === '') {
                    $translations[$lang] = '';

                    continue;
                }

                try {
                    $translations[$lang] = autoGoogleTranslator($lang, $value);
                } catch (Throwable $e) {
                    Log::warning('Auto-translate failed, keeping existing or source value.', [
                        'locale' => $lang,
                        'error' => $e->getMessage(),
                    ]);
                    $translations[$lang] = $existing[$lang] ?? $value;
                }
            }

            return $translations;
        }

        if ($existing !== null) {
            $translations = $existing;
            $translations[$locale] = $value;

            return $translations;
        }

        return [$locale => $value];
    }
}

if (! function_exists('buildTranslations')) {
    /**
     * Build translations for multiple fields at once.
     *
     * @param  array<string, string|null>  $fields
     * @param  array<string, array<string, string>>|null  $existing
     * @return array<string, array<string, string>>
     */
    function buildTranslations(array $fields, bool $autoTranslate, ?array $existing = null): array
    {
        $result = [];

        foreach ($fields as $field => $value) {
            $result[$field] = buildFieldTranslations(
                $value === null ? null : (string) $value,
                $autoTranslate,
                $existing[$field] ?? null
            );
        }

        return $result;
    }
}

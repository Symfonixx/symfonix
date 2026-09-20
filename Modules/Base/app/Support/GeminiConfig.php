<?php

namespace Modules\Base\Support;

use Modules\Base\Models\Settings;

class GeminiConfig
{
    public const KEYS = [
        'gemini_api_key',
        'gemini_image_model',
        'gemini_analysis_model',
    ];

    public const ENV_MAP = [
        'gemini_api_key' => 'GEMINI_API_KEY',
        'gemini_image_model' => 'GEMINI_IMAGE_MODEL',
        'gemini_analysis_model' => 'GEMINI_ANALYSIS_MODEL',
    ];

    public const DEFAULT_IMAGE_MODEL = 'gemini-2.5-flash-image';

    public const DEFAULT_ANALYSIS_MODEL = 'gemini-2.5-flash';

    public static function get(string $key, ?string $default = null): ?string
    {
        try {
            $fromDb = Settings::get($key);
            if (is_string($fromDb) && $fromDb !== '') {
                return $fromDb;
            }
        } catch (\Throwable) {
            //
        }

        $fromConfig = self::fromEnvOrConfig($key);
        if ($fromConfig !== null) {
            return $fromConfig;
        }

        if ($key === 'gemini_image_model') {
            return $default ?? self::DEFAULT_IMAGE_MODEL;
        }

        if ($key === 'gemini_analysis_model') {
            return $default ?? self::DEFAULT_ANALYSIS_MODEL;
        }

        return $default;
    }

    /**
     * @return array<string, string>
     */
    public static function stored(): array
    {
        $values = [];

        foreach (self::KEYS as $key) {
            try {
                $value = Settings::get($key);
                $values[$key] = is_string($value) ? $value : '';
            } catch (\Throwable) {
                $values[$key] = '';
            }
        }

        return $values;
    }

    /**
     * @return array<string, string|null>
     */
    public static function resolved(): array
    {
        return [
            'gemini_api_key' => self::get('gemini_api_key'),
            'gemini_image_model' => self::get('gemini_image_model', self::DEFAULT_IMAGE_MODEL),
            'gemini_analysis_model' => self::get('gemini_analysis_model', self::DEFAULT_ANALYSIS_MODEL),
        ];
    }

    public static function isConfigured(): bool
    {
        return filled(self::get('gemini_api_key'));
    }

    public static function sourceLabel(string $key): string
    {
        try {
            $fromDb = Settings::get($key);
            if (is_string($fromDb) && $fromDb !== '') {
                return 'database';
            }
        } catch (\Throwable) {
            //
        }

        if (self::fromEnvOrConfig($key) !== null) {
            return 'env';
        }

        return 'default';
    }

    private static function fromEnvOrConfig(string $key): ?string
    {
        $envKey = self::ENV_MAP[$key] ?? null;
        if ($envKey) {
            $fromEnv = env($envKey);
            if (is_string($fromEnv) && $fromEnv !== '') {
                return $fromEnv;
            }
        }

        $configKey = match ($key) {
            'gemini_api_key' => 'services.gemini.api_key',
            'gemini_image_model' => 'services.gemini.image_model',
            'gemini_analysis_model' => 'services.gemini.analysis_model',
            default => null,
        };

        if ($configKey === null) {
            return null;
        }

        $fromConfig = config($configKey);
        if (is_string($fromConfig) && $fromConfig !== '') {
            return $fromConfig;
        }

        return null;
    }

    public static function mergeIntoConfig(): void
    {
        $resolved = self::resolved();

        config([
            'services.gemini.api_key' => $resolved['gemini_api_key'],
            'services.gemini.image_model' => $resolved['gemini_image_model'] ?: self::DEFAULT_IMAGE_MODEL,
            'services.gemini.analysis_model' => $resolved['gemini_analysis_model'] ?: self::DEFAULT_ANALYSIS_MODEL,
        ]);
    }
}

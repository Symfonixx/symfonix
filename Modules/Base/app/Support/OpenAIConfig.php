<?php

namespace Modules\Base\Support;

use Modules\Base\Models\Settings;

class OpenAIConfig
{
    public const KEYS = [
        'openai_api_key',
        'openai_model',
    ];

    public const ENV_MAP = [
        'openai_api_key' => 'OPENAI_API_KEY',
        'openai_model' => 'OPENAI_MODEL',
    ];

    public const DEFAULT_MODEL = 'gpt-4o-mini';

    /**
     * @var list<string>
     */
    public const AVAILABLE_MODELS = [
        'gpt-4o',
        'gpt-4o-mini',
        'gpt-4.1',
        'gpt-4.1-mini',
        'gpt-4-turbo',
        'o4-mini',
    ];

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

        if ($key === 'openai_model') {
            return $default ?? self::DEFAULT_MODEL;
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
            'openai_api_key' => self::get('openai_api_key'),
            'openai_model' => self::get('openai_model', self::DEFAULT_MODEL),
        ];
    }

    public static function isConfigured(): bool
    {
        return filled(self::get('openai_api_key'));
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
            'openai_api_key' => 'services.openai.api_key',
            'openai_model' => 'services.openai.model',
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
            'services.openai.api_key' => $resolved['openai_api_key'],
            'services.openai.model' => $resolved['openai_model'] ?: self::DEFAULT_MODEL,
        ]);
    }
}

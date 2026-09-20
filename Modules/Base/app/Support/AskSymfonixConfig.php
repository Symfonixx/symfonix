<?php

namespace Modules\Base\Support;

use Modules\Base\Models\Settings;

class AskSymfonixConfig
{
    public const KEYS = [
        'ai_assistant_provider',
    ];

    public const ENV_MAP = [
        'ai_assistant_provider' => 'AI_ASSISTANT_PROVIDER',
    ];

    public const DEFAULT_PROVIDER = 'auto';

    /**
     * @var list<string>
     */
    public const AVAILABLE_PROVIDERS = [
        'auto',
        'openai',
        'gemini',
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

        if ($key === 'ai_assistant_provider') {
            return $default ?? self::DEFAULT_PROVIDER;
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
            'ai_assistant_provider' => self::provider(),
        ];
    }

    public static function provider(): string
    {
        $value = strtolower((string) self::get('ai_assistant_provider', self::DEFAULT_PROVIDER));

        if (! in_array($value, self::AVAILABLE_PROVIDERS, true)) {
            return self::DEFAULT_PROVIDER;
        }

        return $value;
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

        $fromConfig = config('ai.assistant.provider');
        if (is_string($fromConfig) && $fromConfig !== '') {
            return $fromConfig;
        }

        return null;
    }

    public static function mergeIntoConfig(): void
    {
        config([
            'ai.assistant.provider' => self::provider(),
        ]);
    }
}

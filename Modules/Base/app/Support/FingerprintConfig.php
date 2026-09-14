<?php

namespace Modules\Base\Support;

use Modules\Base\Models\Settings;

class FingerprintConfig
{
    public const KEYS = [
        'fingerprint_enabled',
        'fingerprint_host',
        'fingerprint_port',
        'fingerprint_comm_key',
        'fingerprint_timeout',
        'fingerprint_name_encoding',
        'fingerprint_last_sync_at',
    ];

    public const ENV_MAP = [
        'fingerprint_enabled' => 'FINGERPRINT_ENABLED',
        'fingerprint_host' => 'FINGERPRINT_HOST',
        'fingerprint_port' => 'FINGERPRINT_PORT',
        'fingerprint_comm_key' => 'FINGERPRINT_COMM_KEY',
        'fingerprint_timeout' => 'FINGERPRINT_TIMEOUT',
        'fingerprint_name_encoding' => 'FINGERPRINT_NAME_ENCODING',
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

        $envKey = self::ENV_MAP[$key] ?? null;
        if ($envKey) {
            $fromEnv = env($envKey);
            if (is_string($fromEnv) && $fromEnv !== '') {
                return $fromEnv;
            }
        }

        return match ($key) {
            'fingerprint_port' => $default ?? '4370',
            'fingerprint_comm_key' => $default ?? '0',
            'fingerprint_timeout' => $default ?? '10',
            'fingerprint_name_encoding' => $default ?? 'UTF-8',
            'fingerprint_enabled' => $default ?? '0',
            default => $default,
        };
    }

    public static function isEnabled(): bool
    {
        return filter_var(self::get('fingerprint_enabled', '0'), FILTER_VALIDATE_BOOLEAN);
    }

    public static function isConfigured(): bool
    {
        return self::isEnabled() && filled(self::get('fingerprint_host'));
    }

    /**
     * @return array<string, string|null>
     */
    public static function resolved(): array
    {
        return [
            'enabled' => self::get('fingerprint_enabled', '0'),
            'host' => self::get('fingerprint_host'),
            'port' => self::get('fingerprint_port', '4370'),
            'comm_key' => self::get('fingerprint_comm_key', '0'),
            'timeout' => self::get('fingerprint_timeout', '10'),
            'name_encoding' => self::get('fingerprint_name_encoding', 'UTF-8'),
            'last_sync_at' => self::get('fingerprint_last_sync_at'),
        ];
    }

    public static function mergeIntoConfig(): void
    {
        $resolved = self::resolved();

        config([
            'services.fingerprint.enabled' => filter_var($resolved['enabled'], FILTER_VALIDATE_BOOLEAN),
            'services.fingerprint.host' => $resolved['host'],
            'services.fingerprint.port' => (int) ($resolved['port'] ?: 4370),
            'services.fingerprint.comm_key' => (int) ($resolved['comm_key'] ?: 0),
            'services.fingerprint.timeout' => (float) ($resolved['timeout'] ?: 10),
            'services.fingerprint.name_encoding' => $resolved['name_encoding'] ?: 'UTF-8',
        ]);
    }
}

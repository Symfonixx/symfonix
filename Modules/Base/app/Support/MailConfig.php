<?php

namespace Modules\Base\Support;

use Modules\Base\Models\Settings;

class MailConfig
{
    public const KEYS = [
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
    ];

    public const ENV_MAP = [
        'mail_mailer' => 'MAIL_MAILER',
        'mail_host' => 'MAIL_HOST',
        'mail_port' => 'MAIL_PORT',
        'mail_username' => 'MAIL_USERNAME',
        'mail_password' => 'MAIL_PASSWORD',
        'mail_encryption' => 'MAIL_ENCRYPTION',
        'mail_from_address' => 'MAIL_FROM_ADDRESS',
        'mail_from_name' => 'MAIL_FROM_NAME',
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

        return $default;
    }

    /**
     * Values stored in the settings table only (no .env fallback).
     *
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
     * Effective runtime values (DB first, then .env / config).
     *
     * @return array<string, string|null>
     */
    public static function resolved(): array
    {
        return [
            'mail_mailer' => self::get('mail_mailer', (string) config('mail.default', 'smtp')),
            'mail_host' => self::get('mail_host', (string) config('mail.mailers.smtp.host')),
            'mail_port' => self::get('mail_port', (string) config('mail.mailers.smtp.port')),
            'mail_username' => self::get('mail_username', config('mail.mailers.smtp.username')),
            'mail_password' => self::get('mail_password', config('mail.mailers.smtp.password')),
            'mail_encryption' => self::get('mail_encryption', config('mail.mailers.smtp.encryption')),
            'mail_from_address' => self::get('mail_from_address', config('mail.from.address')),
            'mail_from_name' => self::get('mail_from_name', config('mail.from.name')),
        ];
    }

    public static function isConfigured(): bool
    {
        $mailer = self::get('mail_mailer', (string) config('mail.default'));

        if (in_array($mailer, ['log', 'array'], true)) {
            return filled(self::get('mail_from_address', config('mail.from.address')));
        }

        return filled(self::get('mail_host', config('mail.mailers.smtp.host')))
            && filled(self::get('mail_from_address', config('mail.from.address')));
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

        $envKey = self::ENV_MAP[$key] ?? null;
        if ($envKey && filled(env($envKey))) {
            return 'env';
        }

        return 'default';
    }

    public static function mergeIntoConfig(): void
    {
        $resolved = self::resolved();

        $mailer = $resolved['mail_mailer'] ?: config('mail.default');

        config([
            'mail.default' => $mailer,
            'mail.from.address' => $resolved['mail_from_address'] ?: config('mail.from.address'),
            'mail.from.name' => $resolved['mail_from_name'] ?: config('mail.from.name'),
            'mail.mailers.smtp.host' => $resolved['mail_host'] ?: config('mail.mailers.smtp.host'),
            'mail.mailers.smtp.port' => $resolved['mail_port'] ?: config('mail.mailers.smtp.port'),
            'mail.mailers.smtp.username' => $resolved['mail_username'],
            'mail.mailers.smtp.password' => $resolved['mail_password'],
            'mail.mailers.smtp.encryption' => $resolved['mail_encryption'] ?: config('mail.mailers.smtp.encryption'),
        ]);
    }
}

<?php

namespace Modules\Base\Support;

use Modules\Base\Models\Settings;

class WhatsAppConfig
{
    public const KEYS = [
        'whatsapp_api_token',
        'whatsapp_phone_number_id',
        'whatsapp_business_account_id',
        'whatsapp_api_version',
        'whatsapp_webhook_verify_token',
    ];

    public const ENV_MAP = [
        'whatsapp_api_token' => 'WHATSAPP_API_TOKEN',
        'whatsapp_phone_number_id' => 'WHATSAPP_PHONE_NUMBER_ID',
        'whatsapp_business_account_id' => 'WHATSAPP_BUSINESS_ACCOUNT_ID',
        'whatsapp_api_version' => 'WHATSAPP_API_VERSION',
        'whatsapp_webhook_verify_token' => 'WHATSAPP_WEBHOOK_VERIFY_TOKEN',
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

        if ($key === 'whatsapp_api_version') {
            return $default ?? 'v21.0';
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
            'whatsapp_api_token' => self::get('whatsapp_api_token'),
            'whatsapp_phone_number_id' => self::get('whatsapp_phone_number_id'),
            'whatsapp_business_account_id' => self::get('whatsapp_business_account_id'),
            'whatsapp_api_version' => self::get('whatsapp_api_version', 'v21.0'),
            'whatsapp_webhook_verify_token' => self::get('whatsapp_webhook_verify_token'),
        ];
    }

    public static function isConfigured(): bool
    {
        return filled(self::get('whatsapp_api_token'))
            && filled(self::get('whatsapp_phone_number_id'));
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

        config([
            'services.whatsapp.api_token' => $resolved['whatsapp_api_token'],
            'services.whatsapp.phone_number_id' => $resolved['whatsapp_phone_number_id'],
            'services.whatsapp.business_account_id' => $resolved['whatsapp_business_account_id'],
            'services.whatsapp.api_version' => $resolved['whatsapp_api_version'] ?: 'v21.0',
            'services.whatsapp.webhook_verify_token' => $resolved['whatsapp_webhook_verify_token'],
        ]);
    }
}

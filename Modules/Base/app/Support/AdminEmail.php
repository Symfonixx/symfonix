<?php

namespace Modules\Base\Support;

use Modules\Base\Models\Settings;

class AdminEmail
{
    public static function get(): ?string
    {
        try {
            $fromDb = Settings::get('admin_email');
            if (is_string($fromDb) && $fromDb !== '') {
                return $fromDb;
            }
        } catch (\Throwable) {
            //
        }

        $fromEnv = env('ADMIN_EMAIL');

        return is_string($fromEnv) && $fromEnv !== '' ? $fromEnv : null;
    }

    /**
     * @return list<string>
     */
    public static function addresses(): array
    {
        $emailSetting = self::get() ?: config('mail.from.address');
        if (! $emailSetting) {
            return [];
        }

        $emails = preg_split('/[,\s;]+/', $emailSetting);
        $emails = array_filter($emails, fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL));

        return array_values(array_unique($emails));
    }
}

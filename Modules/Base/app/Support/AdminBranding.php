<?php

namespace Modules\Base\Support;

use Illuminate\Support\Facades\Storage;
use Modules\Base\Models\Settings;

class AdminBranding
{
    public static function logoUrl(): string
    {
        return self::storedOrAsset('logo', 'images/admin_logo.png');
    }

    public static function minLogoUrl(): string
    {
        $stored = self::storedPath('min_logo');

        if ($stored) {
            return asset('storage/'.$stored);
        }

        return self::logoUrl();
    }

    private static function storedOrAsset(string $key, string $defaultAsset): string
    {
        $stored = self::storedPath($key);

        return $stored ? asset('storage/'.$stored) : asset($defaultAsset);
    }

    private static function storedPath(string $key): ?string
    {
        try {
            $value = Settings::get($key);
        } catch (\Throwable) {
            return null;
        }

        if (! is_string($value) || $value === '') {
            return null;
        }

        return Storage::disk('public')->exists($value) ? $value : null;
    }
}

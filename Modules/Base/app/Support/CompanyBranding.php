<?php

namespace Modules\Base\Support;

use Modules\Base\Models\Seo;
use Modules\Base\Models\Settings;

class CompanyBranding
{
    /**
     * @return array{
     *     name: string,
     *     logo: string|null,
     *     logo_path: string|null,
     *     logo_url: string|null,
     *     sign_image: string|null,
     *     sign_path: string|null,
     *     sign_url: string|null,
     *     phone: string|null,
     *     email: string|null,
     *     address: string|null
     * }
     */
    public static function forInvoice(): array
    {
        $siteLogo = self::setting('site_logo');
        $signImage = self::setting('company_sign_image');
        if ($signImage === 'default.jpg') {
            $signImage = null;
        }

        return [
            'name' => Seo::get('website_name', config('app.name')),
            'logo' => $siteLogo,
            'logo_path' => self::imagePath($siteLogo),
            'logo_url' => self::imageUrl($siteLogo),
            'sign_image' => $signImage,
            'sign_path' => self::imagePath($signImage),
            'sign_url' => self::imageUrl($signImage),
            'phone' => self::setting('company_phone') ?? self::setting('phone'),
            'email' => self::setting('company_email') ?? self::setting('email'),
            'address' => self::setting('company_address') ?? self::setting('address'),
        ];
    }

    private static function setting(string $key): ?string
    {
        try {
            $value = Settings::get($key);
        } catch (\Throwable) {
            return null;
        }

        return is_string($value) && $value !== '' ? $value : null;
    }

    private static function imagePath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $absolute = public_path('storage/'.$path);

        return is_file($absolute) ? $absolute : null;
    }

    private static function imageUrl(?string $path): ?string
    {
        return $path ? asset('storage/'.$path) : null;
    }
}

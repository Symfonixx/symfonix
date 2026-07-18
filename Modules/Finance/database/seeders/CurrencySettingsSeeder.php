<?php

namespace Modules\Finance\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Base\Models\Settings;
use Modules\Finance\Models\ExchangeRate;
use Modules\Finance\Services\CurrencyService;

class CurrencySettingsSeeder extends Seeder
{
    public function run(): void
    {
        $default = strtoupper((string) config('finance.default_currency', 'USD'));

        if (! Settings::get(CurrencyService::SETTINGS_DEFAULT_CURRENCY)) {
            Settings::set(CurrencyService::SETTINGS_DEFAULT_CURRENCY, $default);
        }

        if (Settings::get(CurrencyService::SETTINGS_FIXER_API_KEY) === false) {
            Settings::set(CurrencyService::SETTINGS_FIXER_API_KEY, (string) config('services.fixer.api_key', ''));
        }

        $this->seedBaselineRates($default);
    }

    /**
     * Store rates in Fixer shape: 1 base_currency = rate target_currency.
     */
    private function seedBaselineRates(string $base): void
    {
        // Approximate demo quotes: 1 USD = N target.
        $usdQuotes = [
            'USD' => 1.0,
            'EUR' => 0.92,
            'GBP' => 0.79,
            'TRY' => 34.50,
        ];

        $now = now();
        $supported = array_map(
            'strtoupper',
            (array) config('finance.supported_currencies', array_keys($usdQuotes))
        );

        $usdPerOneBase = $base === 'USD'
            ? 1.0
            : (1 / max($usdQuotes[$base] ?? 1.0, 1e-12));

        foreach ($supported as $target) {
            $usdPerOneTarget = $target === 'USD'
                ? 1.0
                : (1 / max($usdQuotes[$target] ?? 1.0, 1e-12));

            // 1 base = (usd value of 1 base) / (usd value of 1 target) targets
            $rate = $usdPerOneBase / $usdPerOneTarget;

            ExchangeRate::query()->updateOrCreate(
                [
                    'base_currency' => $base,
                    'target_currency' => $target,
                ],
                [
                    'rate' => $rate,
                    'fetched_at' => $now,
                ]
            );
        }

        app(CurrencyService::class)->forgetRateCache();
    }
}

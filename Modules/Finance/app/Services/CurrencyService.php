<?php

namespace Modules\Finance\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Base\Models\Settings;
use Modules\Finance\Models\ExchangeRate;

class CurrencyService
{
    public const SESSION_KEY = 'display_currency';

    public const SETTINGS_DEFAULT_CURRENCY = 'default_currency';

    public const SETTINGS_FIXER_API_KEY = 'fixer_api_key';

    /**
     * @return list<string>
     */
    public function supportedCurrencies(): array
    {
        return array_values(array_unique(array_map(
            'strtoupper',
            (array) config('finance.supported_currencies', ['USD', 'EUR', 'GBP', 'TRY'])
        )));
    }

    public function defaultCurrency(): string
    {
        $fromSettings = Settings::get(self::SETTINGS_DEFAULT_CURRENCY);

        if (is_string($fromSettings) && strlen(trim($fromSettings)) === 3) {
            return strtoupper(trim($fromSettings));
        }

        return strtoupper((string) config('finance.default_currency', 'USD'));
    }

    public function displayCurrency(): string
    {
        $sessionCurrency = session(self::SESSION_KEY);

        if (is_string($sessionCurrency) && $this->isSupported($sessionCurrency)) {
            return strtoupper($sessionCurrency);
        }

        return $this->defaultCurrency();
    }

    public function setDisplayCurrency(string $currency): void
    {
        $currency = strtoupper(trim($currency));

        if (! $this->isSupported($currency)) {
            throw new \InvalidArgumentException("Unsupported currency: {$currency}");
        }

        session([self::SESSION_KEY => $currency]);
    }

    public function isSupported(string $currency): bool
    {
        return in_array(strtoupper(trim($currency)), $this->supportedCurrencies(), true);
    }

    public function fixerApiKey(): ?string
    {
        $fromSettings = Settings::get(self::SETTINGS_FIXER_API_KEY);

        if (is_string($fromSettings) && $fromSettings !== '') {
            return $fromSettings;
        }

        $fromConfig = config('services.fixer.api_key');

        return is_string($fromConfig) && $fromConfig !== '' ? $fromConfig : null;
    }

    /**
     * Rate expressing how many units of $to equal 1 unit of $from.
     */
    public function getRate(string $from, string $to): float
    {
        $from = strtoupper(trim($from));
        $to = strtoupper(trim($to));

        if ($from === $to) {
            return 1.0;
        }

        $cacheKey = "exchange_rate:{$from}:{$to}";

        return (float) Cache::remember($cacheKey, now()->addMinutes(15), function () use ($from, $to) {
            $direct = ExchangeRate::query()
                ->where('base_currency', $from)
                ->where('target_currency', $to)
                ->value('rate');

            if ($direct !== null) {
                return (float) $direct;
            }

            $inverse = ExchangeRate::query()
                ->where('base_currency', $to)
                ->where('target_currency', $from)
                ->value('rate');

            if ($inverse !== null && (float) $inverse > 0) {
                return 1 / (float) $inverse;
            }

            $base = $this->defaultCurrency();

            if ($from !== $base && $to !== $base) {
                $fromToBase = $this->getRate($from, $base);
                $baseToTarget = $this->getRate($base, $to);

                return $fromToBase * $baseToTarget;
            }

            return 1.0;
        });
    }

    /**
     * Snapshot rate from transaction currency into the system default (base) currency.
     */
    public function snapshotRateToBase(string $currency): float
    {
        return $this->getRate($currency, $this->defaultCurrency());
    }

    public function convert(float $amount, string $from, ?string $to = null): float
    {
        $to = $to ?? $this->displayCurrency();
        $rate = $this->getRate($from, $to);

        return round($amount * $rate, 2);
    }

    /**
     * Convert a historical ledger amount using the rate stored at posting time
     * (source → base), then apply the latest base → display rate.
     */
    public function convertFromBaseAmount(float $baseAmount, ?string $displayCurrency = null): float
    {
        $displayCurrency = $displayCurrency ?? $this->displayCurrency();
        $base = $this->defaultCurrency();

        return $this->convert($baseAmount, $base, $displayCurrency);
    }

    public function convertUsingHistoricalRate(
        float $amount,
        string $currency,
        float $exchangeRateToBase,
        ?string $displayCurrency = null
    ): float {
        $baseAmount = round($amount * $exchangeRateToBase, 2);

        return $this->convertFromBaseAmount($baseAmount, $displayCurrency);
    }

    public function format(float $amount, ?string $currency = null, int $decimals = 2): string
    {
        $currency = strtoupper($currency ?? $this->displayCurrency());

        return number_format($amount, $decimals).' '.$currency;
    }

    public function forgetRateCache(): void
    {
        foreach ($this->supportedCurrencies() as $from) {
            foreach ($this->supportedCurrencies() as $to) {
                Cache::forget("exchange_rate:{$from}:{$to}");
            }
        }
    }

    /**
     * @return array{default: string, display: string, supported: list<string>}
     */
    public function sharePayload(): array
    {
        return [
            'default' => $this->defaultCurrency(),
            'display' => $this->displayCurrency(),
            'supported' => $this->supportedCurrencies(),
        ];
    }
}

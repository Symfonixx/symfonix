<?php

namespace Modules\Finance\Support;

use Modules\Finance\Services\CurrencyService;

class Money
{
    public static function convert(float $amount, string $from, ?string $to = null): float
    {
        return app(CurrencyService::class)->convert($amount, $from, $to);
    }

    public static function format(float $amount, ?string $currency = null, int $decimals = 2): string
    {
        return app(CurrencyService::class)->format($amount, $currency, $decimals);
    }

    /**
     * Convert an amount from its source currency into the active display currency and format it.
     */
    public static function display(float $amount, string $fromCurrency, ?string $displayCurrency = null): string
    {
        $service = app(CurrencyService::class);
        $displayCurrency = $displayCurrency ?? $service->displayCurrency();
        $converted = $service->convert($amount, $fromCurrency, $displayCurrency);

        return $service->format($converted, $displayCurrency);
    }

    /**
     * Format a journal/ledger amount using the historical base snapshot when available.
     */
    public static function displayLedger(
        float $amount,
        string $currency,
        ?float $exchangeRate = null,
        ?float $baseAmount = null
    ): string {
        $service = app(CurrencyService::class);
        $displayCurrency = $service->displayCurrency();

        if ($baseAmount !== null) {
            $converted = $service->convertFromBaseAmount((float) $baseAmount, $displayCurrency);
        } elseif ($exchangeRate !== null) {
            $converted = $service->convertUsingHistoricalRate($amount, $currency, (float) $exchangeRate, $displayCurrency);
        } else {
            $converted = $service->convert($amount, $currency, $displayCurrency);
        }

        return $service->format($converted, $displayCurrency);
    }
}

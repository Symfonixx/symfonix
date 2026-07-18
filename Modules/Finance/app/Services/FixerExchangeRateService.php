<?php

namespace Modules\Finance\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\ExchangeRate;

class FixerExchangeRateService
{
    public function __construct(
        private readonly CurrencyService $currencyService
    ) {}

    /**
     * Fetch latest rates from Fixer.io for the system default (base) currency.
     *
     * Free Fixer plans only allow EUR as base — non-EUR bases are derived via cross-rates.
     *
     * @return array{base: string, updated: int, rates: array<string, float>}
     */
    public function sync(?string $baseCurrency = null): array
    {
        $baseCurrency = strtoupper($baseCurrency ?? $this->currencyService->defaultCurrency());
        $apiKey = $this->currencyService->fixerApiKey();

        if (! $apiKey) {
            throw new \RuntimeException('Fixer API key is not configured. Set it under System Configurations → Finance.');
        }

        $targets = array_values(array_filter(
            $this->currencyService->supportedCurrencies(),
            fn (string $code) => $code !== $baseCurrency
        ));

        if ($targets === []) {
            $this->storeRate($baseCurrency, $baseCurrency, 1.0);

            return ['base' => $baseCurrency, 'updated' => 1, 'rates' => [$baseCurrency => 1.0]];
        }

        // Free plans reject non-EUR bases; go straight to EUR cross-rates for those.
        if ($baseCurrency !== 'EUR') {
            return $this->syncViaEurBase($baseCurrency, $targets);
        }

        $payload = $this->fetchLatest('EUR', $targets);

        if (! ($payload['success'] ?? false)) {
            if ($this->isBaseNotSupportedError($payload)) {
                return $this->syncViaEurBase($baseCurrency, $targets);
            }

            throw new \RuntimeException($this->formatFixerError($payload));
        }

        $rates = (array) ($payload['rates'] ?? []);
        $updated = $this->persistRates($baseCurrency, $rates);

        Log::info('Fixer exchange rates synced.', [
            'base' => $baseCurrency,
            'updated' => $updated,
        ]);

        return [
            'base' => $baseCurrency,
            'updated' => $updated,
            'rates' => $rates,
        ];
    }

    /**
     * @param  list<string>  $targets
     * @return array{base: string, updated: int, rates: array<string, float>}
     */
    private function syncViaEurBase(string $desiredBase, array $targets): array
    {
        $symbols = array_values(array_unique(array_merge([$desiredBase], $targets)));
        $payload = $this->fetchLatest('EUR', $symbols);

        if (! ($payload['success'] ?? false)) {
            throw new \RuntimeException($this->formatFixerError($payload));
        }

        $eurRates = (array) ($payload['rates'] ?? []);
        $eurToBase = (float) ($eurRates[$desiredBase] ?? 0);

        if ($eurToBase <= 0) {
            throw new \RuntimeException("Fixer did not return a rate for {$desiredBase}.");
        }

        $converted = [];

        foreach ($targets as $target) {
            $eurToTarget = (float) ($eurRates[$target] ?? 0);

            if ($eurToTarget <= 0) {
                continue;
            }

            // 1 desiredBase = (eurToTarget / eurToBase) target
            $converted[$target] = $eurToTarget / $eurToBase;
        }

        $updated = $this->persistRates($desiredBase, $converted);

        Log::info('Fixer exchange rates synced via EUR cross-rates.', [
            'base' => $desiredBase,
            'updated' => $updated,
        ]);

        return [
            'base' => $desiredBase,
            'updated' => $updated,
            'rates' => $converted,
        ];
    }

    /**
     * @param  list<string>  $symbols
     * @return array<string, mixed>
     */
    private function fetchLatest(string $base, array $symbols): array
    {
        $apiKey = trim((string) $this->currencyService->fixerApiKey());
        $endpoint = rtrim((string) config('services.fixer.base_url', 'https://data.fixer.io/api'), '/');
        $url = "{$endpoint}/latest";

        $query = [
            'symbols' => implode(',', $symbols),
        ];

        // Free Fixer locks base to EUR; omit base when EUR so the default applies.
        if ($base !== 'EUR') {
            $query['base'] = $base;
        }

        $response = $this->isApilayerEndpoint($endpoint)
            ? Http::timeout(30)
                ->acceptJson()
                ->withHeaders(['apikey' => $apiKey])
                ->get($url, $query)
            : Http::timeout(30)
                ->acceptJson()
                ->get($url, array_merge($query, ['access_key' => $apiKey]));

        return $this->decodePayload($response);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodePayload(Response $response): array
    {
        $payload = $response->json();

        if (! is_array($payload)) {
            $snippet = trim(mb_substr($response->body(), 0, 200));

            throw new \RuntimeException(
                'Fixer API request failed with HTTP '.$response->status()
                .($snippet !== '' ? ': '.$snippet : '.')
            );
        }

        // Some gateways return 4xx with a Fixer-shaped JSON body.
        if (! $response->successful() && ! array_key_exists('success', $payload)) {
            throw new \RuntimeException(
                'Fixer API request failed with HTTP '.$response->status().'.'
                .' Check that FIXER_API_KEY / System Configurations key is valid'
                .' and FIXER_BASE_URL matches your provider (data.fixer.io vs api.apilayer.com/fixer).'
            );
        }

        if (! array_key_exists('success', $payload) && $response->successful()) {
            $payload['success'] = isset($payload['rates']);
        }

        if (! ($payload['success'] ?? false) && ! $response->successful()) {
            $payload['success'] = false;
            $payload['error'] = $payload['error'] ?? [
                'info' => 'HTTP '.$response->status(),
            ];
        }

        return $payload;
    }

    private function isApilayerEndpoint(string $endpoint): bool
    {
        return str_contains(strtolower($endpoint), 'apilayer.com');
    }

    /**
     * @param  array<string, float|int|string>  $rates
     */
    private function persistRates(string $baseCurrency, array $rates): int
    {
        $updated = 0;
        $now = now();

        $this->storeRate($baseCurrency, $baseCurrency, 1.0, $now);
        $updated++;

        foreach ($rates as $target => $rate) {
            $target = strtoupper((string) $target);
            $rate = (float) $rate;

            if ($rate <= 0) {
                continue;
            }

            $this->storeRate($baseCurrency, $target, $rate, $now);
            $updated++;
        }

        $this->currencyService->forgetRateCache();

        return $updated;
    }

    private function storeRate(string $base, string $target, float $rate, $fetchedAt = null): void
    {
        ExchangeRate::query()->updateOrCreate(
            [
                'base_currency' => strtoupper($base),
                'target_currency' => strtoupper($target),
            ],
            [
                'rate' => $rate,
                'fetched_at' => $fetchedAt ?? now(),
            ]
        );
    }

    private function isBaseNotSupportedError(array $payload): bool
    {
        $code = (int) ($payload['error']['code'] ?? 0);
        $type = (string) ($payload['error']['type'] ?? '');
        $info = strtolower((string) ($payload['error']['info'] ?? ''));

        return $code === 105
            || str_contains(strtolower($type), 'base_currency_access_restricted')
            || str_contains($info, 'base currency');
    }

    private function formatFixerError(array $payload): string
    {
        $code = $payload['error']['code'] ?? null;
        $type = $payload['error']['type'] ?? null;
        $info = $payload['error']['info'] ?? null;

        $parts = array_filter([
            is_string($info) && $info !== '' ? $info : null,
            is_string($type) && $type !== '' ? $type : null,
            $code !== null ? "code {$code}" : null,
        ]);

        if ($parts === []) {
            return 'Unknown Fixer API error. Verify your API key and endpoint.';
        }

        $message = 'Fixer API error: '.implode(' — ', $parts);

        if ((int) $code === 101 || str_contains(strtolower((string) $type), 'invalid_access_key')) {
            $message .= ' Update the key under System Configurations → Finance.';
        }

        return $message;
    }
}

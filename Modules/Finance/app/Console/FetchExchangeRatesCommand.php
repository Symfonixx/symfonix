<?php

namespace Modules\Finance\Console;

use Illuminate\Console\Command;
use Modules\Finance\Jobs\FetchExchangeRatesJob;
use Modules\Finance\Services\CurrencyService;
use Modules\Finance\Services\FixerExchangeRateService;

class FetchExchangeRatesCommand extends Command
{
    protected $signature = 'finance:fetch-exchange-rates
                            {--base= : Base currency override (defaults to system default)}
                            {--sync : Run synchronously instead of dispatching a queued job}';

    protected $description = 'Fetch live exchange rates from Fixer.io for the system default currency.';

    public function handle(
        CurrencyService $currencyService,
        FixerExchangeRateService $fixer
    ): int {
        $base = $this->option('base') ?: $currencyService->defaultCurrency();

        if (! $currencyService->fixerApiKey()) {
            $this->components->warn('Fixer API key is not configured. Seeding fallback 1:1 rates for supported currencies.');
            $this->seedFallbackRates($currencyService, $base);

            return self::SUCCESS;
        }

        if ($this->option('sync')) {
            try {
                $result = $fixer->sync($base);
                $this->components->info("Synced {$result['updated']} rates with base {$result['base']}.");
            } catch (\Throwable $e) {
                $this->components->error($e->getMessage());

                return self::FAILURE;
            }

            return self::SUCCESS;
        }

        FetchExchangeRatesJob::dispatch($base);
        $this->components->info("Queued exchange-rate fetch for base currency {$base}.");

        return self::SUCCESS;
    }

    private function seedFallbackRates(CurrencyService $currencyService, string $base): void
    {
        $now = now();

        foreach ($currencyService->supportedCurrencies() as $target) {
            \Modules\Finance\Models\ExchangeRate::query()->updateOrCreate(
                [
                    'base_currency' => strtoupper($base),
                    'target_currency' => strtoupper($target),
                ],
                [
                    'rate' => $target === strtoupper($base) ? 1 : 1,
                    'fetched_at' => $now,
                ]
            );
        }

        $currencyService->forgetRateCache();
        $this->components->info("Fallback rates stored for base {$base}.");
    }
}

<?php

namespace Modules\Finance\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Finance\Services\FixerExchangeRateService;

class FetchExchangeRatesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly ?string $baseCurrency = null
    ) {}

    public function handle(FixerExchangeRateService $fixer): void
    {
        try {
            $result = $fixer->sync($this->baseCurrency);

            Log::info('FetchExchangeRatesJob completed.', $result);
        } catch (\Throwable $e) {
            Log::error('FetchExchangeRatesJob failed: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            throw $e;
        }
    }
}

<?php

namespace Modules\Base\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Modules\Base\Models\Settings;
use Modules\Base\Support\FingerprintConfig;
use Modules\Core\Traits\FileTrait;
use Modules\Finance\Models\ExchangeRate;
use Modules\Finance\Services\CurrencyService;
use Modules\User\Services\Fingerprint\FingerprintConnectionService;

class SystemConfigurationController extends Controller
{
    use FileTrait;

    public function __construct()
    {
        $this->setActive('settings');
    }

    public function index()
    {
        $this->setActive('systemConfigurations');
        $settings = Settings::pluck('value', 'key');

        $currencyService = app(CurrencyService::class);
        $supportedCurrencies = $currencyService->supportedCurrencies();
        $defaultCurrency = $settings->get(
            'default_currency',
            $currencyService->defaultCurrency()
        );
        $hasFixerKey = filled($currencyService->fixerApiKey());
        $latestRateFetchedAt = ExchangeRate::query()
            ->whereNotNull('fetched_at')
            ->max('fetched_at');

        $fingerprintConfigured = FingerprintConfig::isConfigured();

        return view('base::admin.system-configurations.index', compact(
            'settings',
            'supportedCurrencies',
            'defaultCurrency',
            'hasFixerKey',
            'latestRateFetchedAt',
            'fingerprintConfigured',
        ));
    }

    public function store(Request $request)
    {
        if ($request->hasFile('imgs')) {
            foreach ($request->file('imgs') as $key => $file) {
                if (! $file) {
                    continue;
                }

                $oldFile = Settings::get($key) ?: null;
                if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                    Storage::disk('public')->delete($oldFile);
                }

                $path = $file->store('settings', 'public');

                if ($path) {
                    Settings::set($key, $path);
                }
            }
        }

        $data = $request->input('data', []);
        if (is_array($data)) {
            if (array_key_exists('auto_backup_interval_days', $data)) {
                $days = (int) $data['auto_backup_interval_days'];
                $data['auto_backup_interval_days'] = (string) max(1, min(365, $days));
            }

            if (array_key_exists('auto_backup_enabled', $data)) {
                $data['auto_backup_enabled'] = filter_var($data['auto_backup_enabled'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
            }

            if (array_key_exists('fingerprint_enabled', $data)) {
                $data['fingerprint_enabled'] = filter_var($data['fingerprint_enabled'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
            }

            if (array_key_exists('fingerprint_port', $data)) {
                $data['fingerprint_port'] = (string) max(1, min(65535, (int) $data['fingerprint_port']));
            }

            if (array_key_exists('fingerprint_timeout', $data)) {
                $data['fingerprint_timeout'] = (string) max(3, min(60, (int) $data['fingerprint_timeout']));
            }

            if (array_key_exists('fingerprint_comm_key', $data)) {
                $data['fingerprint_comm_key'] = (string) max(0, (int) $data['fingerprint_comm_key']);
            }

            if (array_key_exists('default_currency', $data)) {
                $currency = strtoupper(trim((string) $data['default_currency']));
                $supported = app(CurrencyService::class)->supportedCurrencies();

                if (! in_array($currency, $supported, true)) {
                    $currency = app(CurrencyService::class)->defaultCurrency();
                }

                $data['default_currency'] = $currency;
            }

            foreach ($data as $key => $value) {
                Settings::set($key, $value === null ? '' : $value);
            }
        }

        cache()->forget('settings');
        app(CurrencyService::class)->forgetRateCache();
        session()->flushMessage(true);

        return back();
    }

    public function fetchRates(Request $request)
    {
        $data = $request->input('data', []);

        if (is_array($data)) {
            if (array_key_exists('default_currency', $data)) {
                $currency = strtoupper(trim((string) $data['default_currency']));
                $supported = app(CurrencyService::class)->supportedCurrencies();

                if (in_array($currency, $supported, true)) {
                    Settings::set('default_currency', $currency);
                }
            }

            if (array_key_exists('fixer_api_key', $data)) {
                Settings::set('fixer_api_key', (string) ($data['fixer_api_key'] ?? ''));
            }
        }

        cache()->forget('settings');

        $exitCode = Artisan::call('finance:fetch-exchange-rates', ['--sync' => true]);

        if ($exitCode === 0) {
            session()->flushMessage(true, __('Exchange rates updated successfully.'));
        } else {
            session()->flushMessage(false, trim(Artisan::output()) ?: __('Failed to fetch exchange rates.'));
        }

        return back();
    }

    public function testFingerprint(Request $request, FingerprintConnectionService $connectionService)
    {
        $data = $request->input('data', []);

        if (is_array($data)) {
            foreach ([
                'fingerprint_enabled',
                'fingerprint_host',
                'fingerprint_port',
                'fingerprint_comm_key',
                'fingerprint_timeout',
                'fingerprint_name_encoding',
            ] as $key) {
                if (array_key_exists($key, $data)) {
                    if ($key === 'fingerprint_enabled') {
                        Settings::set($key, filter_var($data[$key], FILTER_VALIDATE_BOOLEAN) ? '1' : '0');
                    } elseif ($key === 'fingerprint_port') {
                        Settings::set($key, (string) max(1, min(65535, (int) $data[$key])));
                    } elseif ($key === 'fingerprint_timeout') {
                        Settings::set($key, (string) max(3, min(60, (int) $data[$key])));
                    } elseif ($key === 'fingerprint_comm_key') {
                        Settings::set($key, (string) max(0, (int) $data[$key]));
                    } else {
                        Settings::set($key, (string) ($data[$key] ?? ''));
                    }
                }
            }
        }

        cache()->forget('settings');

        $result = $connectionService->testConnection();

        if ($result['success']) {
            session()->flushMessage(true, $result['message']);
        } else {
            session()->flushMessage(false, $result['message']);
        }

        return back();
    }
}

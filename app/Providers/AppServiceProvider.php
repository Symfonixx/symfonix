<?php

namespace App\Providers;

use App\Translation\JsonFileLoader;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Modules\Base\Support\AdminEmail;
use Modules\Base\Support\FingerprintConfig;
use Modules\Base\Support\MailConfig;
use Modules\Base\Support\WhatsAppConfig;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->extend('translation.loader', function ($loader, $app) {
            return new JsonFileLoader($app['files'], $app['path.lang']);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        $this->forceHttpsInProduction();
        $this->mergeAdminEmailConfig();
        $this->mergeIntegrationConfigs();

        Gate::define('viewPulse', fn ($user) => $user->can('system.monitoring.view'));
    }

    private function forceHttpsInProduction(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }

    private function mergeAdminEmailConfig(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            $adminEmail = AdminEmail::get();
            if (! $adminEmail) {
                return;
            }

            config([
                'services.admin_email' => $adminEmail,
                'services.leads.admin_email' => $adminEmail,
            ]);
        } catch (\Throwable) {
            //
        }
    }

    private function mergeIntegrationConfigs(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            MailConfig::mergeIntoConfig();
            WhatsAppConfig::mergeIntoConfig();
            FingerprintConfig::mergeIntoConfig();
        } catch (\Throwable) {
            //
        }
    }
}

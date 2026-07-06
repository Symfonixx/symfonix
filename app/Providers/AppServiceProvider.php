<?php

namespace App\Providers;

use App\Translation\JsonFileLoader;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Modules\Base\Support\AdminEmail;

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
        $this->mergeAdminEmailConfig();
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
}

<?php

namespace Modules\Tax\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'Tax';

    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapAdminRoutes();
    }

    protected function mapApiRoutes(): void
    {
        Route::middleware('api')->prefix('api')->name('api.')->group(module_path($this->name, '/routes/api.php'));
    }

    protected function mapWebRoutes(): void
    {
        $name = $this->name;
        Route::group([
            'prefix' => LaravelLocalization::setLocale(),
            'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'visitor_tracking'],
        ], static function () use ($name) {
            Route::middleware(['web'])->group(module_path($name, '/routes/web.php'));
        });
    }

    protected function mapAdminRoutes(): void
    {
        $name = $this->name;
        Route::group([
            'prefix' => LaravelLocalization::setLocale(),
            'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
        ], static function () use ($name) {
            Route::prefix('admin')
                ->name('admin.')
                ->middleware(['web', 'auth', 'is_admin', 'catalog.permission'])
                ->group(module_path($name, '/routes/admin.php'));
        });
    }
}

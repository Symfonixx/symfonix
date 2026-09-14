<?php

namespace Modules\Base\Providers;

use App\Http\Middleware\ContentSecurityPolicy;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\TrackAdminEvents;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Base\Http\Controllers\HumansTxtController;
use Modules\Base\Http\Controllers\LlmsTxtController;
use Modules\Base\Http\Controllers\RssController;
use Modules\Base\Http\Controllers\SitemapController;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'Base';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapAdminRoutes();
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::middleware('api')->prefix('api')->name('api.')->group(module_path($this->name, '/routes/api.php'));
    }

    protected function mapWebRoutes(): void
    {
        $name = $this->name;
        $seoMiddlewareExclusions = [
            HandleInertiaRequests::class,
            ContentSecurityPolicy::class,
            TrackAdminEvents::class,
        ];

        Route::middleware(['web'])
            ->get('/sitemap.xml', [SitemapController::class, 'index'])
            ->name('sitemap')
            ->withoutMiddleware($seoMiddlewareExclusions);
        Route::middleware(['web'])
            ->get('/rss.xml', [RssController::class, 'index'])
            ->name('rss')
            ->withoutMiddleware($seoMiddlewareExclusions);
        Route::middleware(['web'])
            ->get('/humans.txt', [HumansTxtController::class, 'index'])
            ->name('humans.txt')
            ->withoutMiddleware($seoMiddlewareExclusions);
        Route::middleware(['web'])
            ->get('/llms.txt', [LlmsTxtController::class, 'index'])
            ->name('llms.txt')
            ->withoutMiddleware($seoMiddlewareExclusions);
        Route::middleware(['web'])
            ->get('/llms-full.txt', [LlmsTxtController::class, 'full'])
            ->name('llms-full.txt')
            ->withoutMiddleware($seoMiddlewareExclusions);

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

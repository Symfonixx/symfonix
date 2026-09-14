<?php

namespace Modules\Tax\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Tax\Models\TaxRate;
use Modules\Tax\Policies\TaxRatePolicy;
use Modules\Tax\Repositories\TaxRate\TaxRateModelRepository;
use Modules\Tax\Repositories\TaxRate\TaxRateRepository;
use Modules\Tax\Services\TaxCalculationService;
use Modules\Tax\Services\TaxFilingReportService;
use Modules\Tax\Services\TaxLedgerService;
use Modules\Tax\Services\TaxRate\TaxRateService;
use Nwidart\Modules\Traits\PathNamespace;

class TaxServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Tax';

    protected string $nameLower = 'tax';

    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        Gate::policy(TaxRate::class, TaxRatePolicy::class);
        Blade::anonymousComponentPath(module_path($this->name, 'resources/views/components'), 'tax');
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    public function register(): void
    {
        $this->app->singleton(TaxCalculationService::class);
        $this->app->singleton(TaxLedgerService::class);
        $this->app->singleton(TaxFilingReportService::class);
        $this->app->singleton(TaxRateService::class);
        $this->app->bind(TaxRateRepository::class, TaxRateModelRepository::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    protected function registerConfig(): void
    {
        $this->publishes([module_path($this->name, 'config/config.php') => config_path($this->nameLower.'.php')], 'config');
        $this->mergeConfigFrom(module_path($this->name, 'config/config.php'), $this->nameLower);
    }

    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        $componentNamespace = $this->module_namespace($this->name, $this->app_path(config('modules.paths.generator.component-class.path')));
        Blade::componentNamespace($componentNamespace, $this->nameLower);
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}

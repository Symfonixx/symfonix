<?php

namespace Modules\Reporting\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Reporting\Services\EmployeeReportService;
use Modules\Reporting\Services\FinanceReportService;
use Modules\Reporting\Services\MarketingReportService;
use Modules\Reporting\Services\OperationsReportService;
use Modules\Reporting\Services\ReportExportService;
use Modules\Reporting\Services\SalesReportService;
use Nwidart\Modules\Traits\PathNamespace;

class ReportingServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Reporting';

    protected string $nameLower = 'reporting';

    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
    }

    public function register(): void
    {
        $this->app->singleton(FinanceReportService::class);
        $this->app->singleton(SalesReportService::class);
        $this->app->singleton(MarketingReportService::class);
        $this->app->singleton(OperationsReportService::class);
        $this->app->singleton(EmployeeReportService::class);
        $this->app->singleton(ReportExportService::class);
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

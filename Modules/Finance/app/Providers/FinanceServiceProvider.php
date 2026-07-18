<?php

namespace Modules\Finance\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Finance\Console\FetchExchangeRatesCommand;
use Modules\Finance\Console\ProcessSubscriptionRenewalsCommand;
use Modules\Finance\Models\Commission;
use Modules\Finance\Models\ExpenseCategory;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\Salary;
use Modules\Finance\Policies\CommissionPolicy;
use Modules\Finance\Policies\ExpenseCategoryPolicy;
use Modules\Finance\Policies\InvoicePolicy;
use Modules\Finance\Policies\SalaryPolicy;
use Modules\Finance\Repositories\ExpenseCategory\ExpenseCategoryModelRepository;
use Modules\Finance\Repositories\ExpenseCategory\ExpenseCategoryRepository;
use Modules\Finance\Services\CurrencyService;
use Modules\Finance\Services\FinanceService;
use Modules\Finance\Services\FixerExchangeRateService;
use Modules\Finance\Services\InvoiceService;
use Modules\Finance\View\Components\MoneyAmount;
use Nwidart\Modules\Traits\PathNamespace;

class FinanceServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Finance';

    protected string $nameLower = 'finance';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        Gate::policy(Salary::class, SalaryPolicy::class);
        Gate::policy(Commission::class, CommissionPolicy::class);
        Gate::policy(ExpenseCategory::class, ExpenseCategoryPolicy::class);
        Gate::policy(Invoice::class, InvoicePolicy::class);
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        Blade::component('finance-money', MoneyAmount::class);

        \Illuminate\Support\Facades\View::composer('components.admin-layout', function ($view) {
            $currencyService = app(CurrencyService::class);
            $view->with('currencyContext', $currencyService->sharePayload());
        });
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->singleton(CurrencyService::class);
        $this->app->singleton(FixerExchangeRateService::class);
        $this->app->singleton(FinanceService::class);
        $this->app->singleton(InvoiceService::class);
        $this->app->bind(ExpenseCategoryRepository::class, ExpenseCategoryModelRepository::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            ProcessSubscriptionRenewalsCommand::class,
            FetchExchangeRatesCommand::class,
        ]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(\Illuminate\Console\Scheduling\Schedule::class);
            $schedule->command('finance:process-subscription-renewals')->daily();
            $schedule->command('finance:fetch-exchange-rates --sync')
                ->twiceDaily(1, 13)
                ->withoutOverlapping();
        });
    }

    /**
     * Register translations.
     */
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

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([module_path($this->name, 'config/config.php') => config_path($this->nameLower.'.php')], 'config');
        $this->mergeConfigFrom(module_path($this->name, 'config/config.php'), $this->nameLower);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        $componentNamespace = $this->module_namespace($this->name, $this->app_path(config('modules.paths.generator.component-class.path')));
        Blade::componentNamespace($componentNamespace, $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
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

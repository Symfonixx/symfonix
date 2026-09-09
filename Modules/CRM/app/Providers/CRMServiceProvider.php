<?php

namespace Modules\CRM\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Contact;
use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Quote;
use Modules\CRM\Models\Subscription;
use Modules\CRM\Policies\CompanyPolicy;
use Modules\CRM\Policies\ContactPolicy;
use Modules\CRM\Policies\CrmActivityPolicy;
use Modules\CRM\Policies\DealPolicy;
use Modules\CRM\Policies\QuotePolicy;
use Modules\CRM\Policies\SubscriptionPolicy;
use Modules\CRM\Repositories\Company\CompanyModelRepository;
use Modules\CRM\Repositories\Company\CompanyRepository;
use Modules\CRM\Repositories\Contact\ContactModelRepository;
use Modules\CRM\Repositories\Contact\ContactRepository;
use Modules\CRM\Repositories\Deal\DealModelRepository;
use Modules\CRM\Repositories\Deal\DealRepository;
use Modules\CRM\Repositories\Lead\LeadModelRepository;
use Modules\CRM\Repositories\Lead\LeadRepository;
use Modules\CRM\Repositories\PipelineStage\PipelineStageModelRepository;
use Modules\CRM\Repositories\PipelineStage\PipelineStageRepository;
use Modules\CRM\Repositories\Subscription\SubscriptionModelRepository;
use Modules\CRM\Repositories\Subscription\SubscriptionRepository;
use Nwidart\Modules\Traits\PathNamespace;

class CRMServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'CRM';

    protected string $nameLower = 'crm';

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
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(Contact::class, ContactPolicy::class);
        Gate::policy(Deal::class, DealPolicy::class);
        Gate::policy(Quote::class, QuotePolicy::class);
        Gate::policy(Subscription::class, SubscriptionPolicy::class);
        Gate::policy(CrmActivity::class, CrmActivityPolicy::class);
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->bind(CompanyRepository::class, CompanyModelRepository::class);
        $this->app->bind(ContactRepository::class, ContactModelRepository::class);
        $this->app->bind(DealRepository::class, DealModelRepository::class);
        $this->app->bind(LeadRepository::class, LeadModelRepository::class);
        $this->app->bind(PipelineStageRepository::class, PipelineStageModelRepository::class);
        $this->app->bind(SubscriptionRepository::class, SubscriptionModelRepository::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
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

<?php

namespace Modules\AI\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\AI\Services\Assistant\AssistantToolRegistry;
use Modules\AI\Services\Assistant\Tools\DraftFollowUpMessageTool;
use Modules\AI\Services\Assistant\Tools\GetBestSellingServicesTool;
use Modules\AI\Services\Assistant\Tools\GetBusinessSnapshotTool;
use Modules\AI\Services\Assistant\Tools\GetCustomerDetailsTool;
use Modules\AI\Services\Assistant\Tools\GetCustomerStatsTool;
use Modules\AI\Services\Assistant\Tools\GetEmployeeReportTool;
use Modules\AI\Services\Assistant\Tools\GetEmployeeStatsTool;
use Modules\AI\Services\Assistant\Tools\GetExpenseStatsTool;
use Modules\AI\Services\Assistant\Tools\GetInvoiceStatsTool;
use Modules\AI\Services\Assistant\Tools\GetLeadDetailsTool;
use Modules\AI\Services\Assistant\Tools\GetLeadStatsTool;
use Modules\AI\Services\Assistant\Tools\GetOverdueWorkTool;
use Modules\AI\Services\Assistant\Tools\GetPaymentStatsTool;
use Modules\AI\Services\Assistant\Tools\GetProjectDetailsTool;
use Modules\AI\Services\Assistant\Tools\GetProjectStatsTool;
use Modules\AI\Services\Assistant\Tools\GetTicketStatsTool;
use Modules\AI\Services\Assistant\Tools\GetTodayFocusTool;
use Modules\AI\Services\Assistant\Tools\GetTopCustomersTool;
use Modules\AI\Services\Assistant\Tools\GetVisitorStatsTool;
use Modules\AI\Services\Assistant\Tools\SearchRecordsTool;
use Modules\AI\Services\Chatbot\PublicChatToolRegistry;
use Modules\AI\Services\Chatbot\Tools\CaptureWebsiteLeadTool;
use Modules\AI\Services\Chatbot\Tools\GetCompanyInfoTool;
use Modules\AI\Services\Chatbot\Tools\GetPublishedServiceTool;
use Modules\AI\Services\Chatbot\Tools\ListPublishedServicesTool;
use Modules\AI\Services\Chatbot\Tools\SuggestQuickRepliesTool;
use Modules\AI\Support\CostLimiter;
use Nwidart\Modules\Traits\PathNamespace;

class AIServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'AI';

    protected string $nameLower = 'ai';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
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

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        $this->app->singleton(AssistantToolRegistry::class, function ($app) {
            return new AssistantToolRegistry([
                $app->make(GetLeadStatsTool::class),
                $app->make(GetLeadDetailsTool::class),
                $app->make(GetCustomerStatsTool::class),
                $app->make(GetCustomerDetailsTool::class),
                $app->make(GetTopCustomersTool::class),
                $app->make(GetProjectStatsTool::class),
                $app->make(GetProjectDetailsTool::class),
                $app->make(GetInvoiceStatsTool::class),
                $app->make(GetPaymentStatsTool::class),
                $app->make(GetExpenseStatsTool::class),
                $app->make(GetEmployeeStatsTool::class),
                $app->make(GetEmployeeReportTool::class),
                $app->make(GetTicketStatsTool::class),
                $app->make(GetOverdueWorkTool::class),
                $app->make(GetVisitorStatsTool::class),
                $app->make(GetBestSellingServicesTool::class),
                $app->make(SearchRecordsTool::class),
                $app->make(DraftFollowUpMessageTool::class),
                $app->make(GetBusinessSnapshotTool::class),
                $app->make(GetTodayFocusTool::class),
            ], $app->make(CostLimiter::class));
        });

        $this->app->singleton(PublicChatToolRegistry::class, function ($app) {
            return new PublicChatToolRegistry([
                $app->make(ListPublishedServicesTool::class),
                $app->make(GetPublishedServiceTool::class),
                $app->make(GetCompanyInfoTool::class),
                $app->make(CaptureWebsiteLeadTool::class),
                $app->make(SuggestQuickRepliesTool::class),
            ], $app->make(CostLimiter::class));
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}

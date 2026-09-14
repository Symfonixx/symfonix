<?php

namespace Modules\User\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Base\Support\FingerprintConfig;
use Modules\User\app\Repositories\Employee\EmployeeModelRepository;
use Modules\User\app\Repositories\Employee\EmployeeRepository;
use Modules\User\app\Repositories\Leave\LeaveModelRepository;
use Modules\User\app\Repositories\Leave\LeaveRepository;
use Modules\User\app\Repositories\User\UserModelRepository;
use Modules\User\app\Repositories\User\UserRepository;
use Modules\User\Console\SyncFingerprintAttendanceCommand;
use Modules\User\Console\SyncPermissionsCommand;
use Modules\User\Repositories\Role\RoleModelRepository;
use Modules\User\Repositories\Role\RoleRepository;
use Modules\User\Services\Fingerprint\FingerprintAttendanceSyncService;
use Modules\User\Services\Fingerprint\FingerprintConnectionService;
use Modules\User\Services\Fingerprint\FingerprintDeviceFactory;
use Modules\User\Services\Fingerprint\FingerprintEnrollmentService;
use Modules\User\Support\PermissionCatalog;
use Nwidart\Modules\Traits\PathNamespace;

class UserServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'User';

    protected string $nameLower = 'user';

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
        $this->registerPermissionDirectives();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    protected function registerPermissionDirectives(): void
    {
        $helpers = module_path($this->name, 'app/Helpers/permissions.php');
        if (is_file($helpers)) {
            require_once $helpers;
        }

        Blade::if('canTab', function (string|array $tab) {
            $user = auth()->user();

            return $user?->canany(PermissionCatalog::keysFor($tab)) ?? false;
        });

        Blade::if('canSection', function (string $section) {
            $user = auth()->user();

            return $user?->canany(PermissionCatalog::keysForSection($section)) ?? false;
        });
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            SyncFingerprintAttendanceCommand::class,
            SyncPermissionsCommand::class,
        ]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('user:sync-fingerprint-attendance')
                ->everyFifteenMinutes()
                ->withoutOverlapping()
                ->when(fn () => FingerprintConfig::isConfigured());
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
        $this->app->register(AuthServiceProvider::class);
        $this->app->bind(RoleRepository::class, RoleModelRepository::class);
        $this->app->bind(UserRepository::class, UserModelRepository::class);
        $this->app->bind(EmployeeRepository::class, EmployeeModelRepository::class);
        $this->app->bind(LeaveRepository::class, LeaveModelRepository::class);

        $this->app->singleton(FingerprintDeviceFactory::class);
        $this->app->singleton(FingerprintConnectionService::class);
        $this->app->singleton(FingerprintEnrollmentService::class);
        $this->app->singleton(FingerprintAttendanceSyncService::class);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}

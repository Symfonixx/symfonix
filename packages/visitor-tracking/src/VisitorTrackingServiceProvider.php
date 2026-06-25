<?php

namespace MonishRoy\VisitorTracking;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use MonishRoy\VisitorTracking\Middleware\VisitorTracking;

class VisitorTrackingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register the middleware alias
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('visitor_tracking', VisitorTracking::class);

        // Publish migration
        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations'),
        ], 'migrations');
        
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function register()
    {
        //
    }
}

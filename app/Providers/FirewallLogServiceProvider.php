<?php

namespace App\Providers;

use Akaunting\Firewall\Models\Log;
use App\Observers\FirewallLogObserver;
use Illuminate\Support\ServiceProvider;

class FirewallLogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register observer for Firewall Log model to handle UTF-8 encoding
        Log::observe(FirewallLogObserver::class);
        
        // Load firewall translations from resources/lang to override package translations
        $this->loadTranslationsFrom(resource_path('lang'), 'firewall');
    }
}

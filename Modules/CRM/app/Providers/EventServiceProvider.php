<?php

namespace Modules\CRM\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\CRM\Events\DealStageChanged;
use Modules\CRM\Listeners\SendDealStageChangedNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        DealStageChanged::class => [
            SendDealStageChangedNotification::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void
    {
        //
    }
}

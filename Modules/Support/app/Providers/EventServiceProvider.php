<?php

namespace Modules\Support\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Support\app\Events\TicketClosed;
use Modules\Support\app\Events\TicketCreated;
use Modules\Support\app\Events\TicketReplied;
use Modules\Support\app\Events\TicketStatusChanged;
use Modules\Support\app\Listeners\NotifyAdminsOfNewTicket;
use Modules\Support\app\Listeners\NotifyAdminsOfTicketClosed;
use Modules\Support\app\Listeners\NotifyCustomerOfTicketStatusChange;
use Modules\Support\app\Listeners\NotifyOfTicketReply;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        TicketCreated::class => [
            NotifyAdminsOfNewTicket::class,
        ],
        TicketReplied::class => [
            NotifyOfTicketReply::class,
        ],
        TicketStatusChanged::class => [
            NotifyCustomerOfTicketStatusChange::class,
        ],
        TicketClosed::class => [
            NotifyAdminsOfTicketClosed::class,
        ],
    ];

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void
    {
        //
    }
}

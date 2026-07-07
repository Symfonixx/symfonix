<?php

namespace Modules\Project\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Finance\Events\InvoiceSentToCustomer;
use Modules\Finance\Listeners\NotifyCustomerOfInvoiceSent;
use Modules\Project\Events\ProjectPaymentStatusChanged;
use Modules\Project\Events\ProjectStatusChanged;
use Modules\Project\Listeners\NotifyCustomerOfProjectChanges;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        ProjectStatusChanged::class => [
            [NotifyCustomerOfProjectChanges::class, 'handleStatusChanged'],
        ],
        ProjectPaymentStatusChanged::class => [
            [NotifyCustomerOfProjectChanges::class, 'handlePaymentStatusChanged'],
        ],
        InvoiceSentToCustomer::class => [
            NotifyCustomerOfInvoiceSent::class,
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

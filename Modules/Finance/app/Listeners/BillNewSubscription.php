<?php

namespace Modules\Finance\Listeners;

use Modules\CRM\Events\SubscriptionCreated;
use Modules\Finance\Services\InvoiceService;

class BillNewSubscription
{
    public function __construct(private readonly InvoiceService $invoiceService) {}

    /**
     * Invoice the first billing period as soon as a subscription is created.
     */
    public function handle(SubscriptionCreated $event): void
    {
        $this->invoiceService->billInitialSubscription($event->subscription);
    }
}

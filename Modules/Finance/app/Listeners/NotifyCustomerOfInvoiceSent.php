<?php

namespace Modules\Finance\Listeners;

use Illuminate\Support\Facades\Notification;
use Modules\Finance\Events\InvoiceSentToCustomer;
use Modules\Finance\Notifications\InvoiceSentToCustomerNotification;

class NotifyCustomerOfInvoiceSent
{
    public function handle(InvoiceSentToCustomer $event): void
    {
        $event->invoice->loadMissing(['company.user', 'project:id,title']);

        $customer = $event->invoice->company?->user;

        if (! $customer?->isCustomer()) {
            return;
        }

        Notification::send($customer, new InvoiceSentToCustomerNotification($event));
    }
}

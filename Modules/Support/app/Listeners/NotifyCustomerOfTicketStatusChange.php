<?php

namespace Modules\Support\app\Listeners;

use Illuminate\Support\Facades\Notification;
use Modules\Support\app\Events\TicketStatusChanged;
use Modules\Support\app\Notifications\TicketStatusChangedCustomerNotification;

class NotifyCustomerOfTicketStatusChange
{
    public function handle(TicketStatusChanged $event): void
    {
        if ($event->fromStatus === $event->toStatus) {
            return;
        }

        $event->ticket->loadMissing('user:id,name,email');

        $customer = $event->ticket->user;

        if (! $customer?->isCustomer()) {
            return;
        }

        if ($event->changedBy && $event->changedBy->id === $customer->id) {
            return;
        }

        Notification::send($customer, new TicketStatusChangedCustomerNotification($event));
    }
}

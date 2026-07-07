<?php

namespace Modules\Support\app\Listeners;

use Illuminate\Support\Facades\Notification;
use Modules\Support\app\Events\TicketClosed;
use Modules\Support\app\Notifications\TicketClosedAdminNotification;
use Modules\Support\app\Support\TicketNotificationRecipients;

class NotifyAdminsOfTicketClosed
{
    public function handle(TicketClosed $event): void
    {
        if (! $event->closedBy->isCustomer()) {
            return;
        }

        $event->ticket->loadMissing(['user:id,name,email', 'assignee:id,name,email']);

        if ($event->ticket->assignee && $event->ticket->assignee->id !== $event->closedBy->id) {
            $recipients = collect([$event->ticket->assignee]);
        } else {
            $recipients = TicketNotificationRecipients::supportAdmins($event->closedBy);
        }

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new TicketClosedAdminNotification($event));
    }
}

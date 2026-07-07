<?php

namespace Modules\Support\app\Listeners;

use Illuminate\Support\Facades\Notification;
use Modules\Support\app\Events\TicketCreated;
use Modules\Support\app\Notifications\NewTicketAdminNotification;
use Modules\Support\app\Support\TicketNotificationRecipients;

class NotifyAdminsOfNewTicket
{
    public function handle(TicketCreated $event): void
    {
        $event->ticket->loadMissing(['user:id,name,email', 'category:id,name']);

        $recipients = TicketNotificationRecipients::supportAdmins();

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new NewTicketAdminNotification($event));
    }
}

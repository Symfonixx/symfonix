<?php

namespace Modules\Support\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Support\app\Events\TicketClosed;

class TicketClosedAdminNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly TicketClosed $event) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->event->ticket;
        $ticket->loadMissing('user:id,name');

        return (new MailMessage)
            ->subject(__('support::ticket.notifications.closed_admin_subject', [
                'number' => $ticket->ticket_number,
            ]))
            ->greeting(__('support::ticket.notifications.greeting', ['name' => $notifiable->name]))
            ->line(__('support::ticket.notifications.closed_admin_line', [
                'number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'customer' => $this->event->closedBy->name,
            ]))
            ->action(__('support::ticket.notifications.view_ticket'), route('admin.tickets.show', $ticket));
    }

    public function toArray(object $notifiable): array
    {
        $ticket = $this->event->ticket;

        return [
            'type' => 'ticket_closed',
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'message' => __('support::ticket.notifications.closed_admin_line', [
                'number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'customer' => $this->event->closedBy->name,
            ]),
            'url' => route('admin.tickets.show', $ticket),
        ];
    }
}

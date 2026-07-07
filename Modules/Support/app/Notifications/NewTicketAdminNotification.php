<?php

namespace Modules\Support\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Support\app\Events\TicketCreated;

class NewTicketAdminNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly TicketCreated $event) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->event->ticket;
        $ticket->loadMissing(['user:id,name,email', 'category:id,name']);

        return (new MailMessage)
            ->subject(__('support::ticket.notifications.new_ticket_subject', [
                'number' => $ticket->ticket_number,
            ]))
            ->greeting(__('support::ticket.notifications.greeting', ['name' => $notifiable->name]))
            ->line(__('support::ticket.notifications.new_ticket_line', [
                'number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'customer' => $ticket->user?->name ?? __('N/A'),
            ]))
            ->action(__('support::ticket.notifications.view_ticket'), route('admin.tickets.show', $ticket));
    }

    public function toArray(object $notifiable): array
    {
        $ticket = $this->event->ticket;
        $ticket->loadMissing(['user:id,name']);

        return [
            'type' => 'ticket_created',
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'customer_name' => $ticket->user?->name,
            'message' => __('support::ticket.notifications.new_ticket_line', [
                'number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'customer' => $ticket->user?->name ?? __('N/A'),
            ]),
            'url' => route('admin.tickets.show', $ticket),
        ];
    }
}

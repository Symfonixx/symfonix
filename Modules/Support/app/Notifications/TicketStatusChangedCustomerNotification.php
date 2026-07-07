<?php

namespace Modules\Support\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Support\app\Events\TicketStatusChanged;

class TicketStatusChangedCustomerNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly TicketStatusChanged $event) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->event->ticket;

        return (new MailMessage)
            ->subject(__('user::portal.notifications.ticket_status_subject', [
                'number' => $ticket->ticket_number,
            ]))
            ->greeting(__('user::portal.notifications.greeting', ['name' => $notifiable->name]))
            ->line(__('user::portal.notifications.ticket_status_line', [
                'number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'from' => __('user::portal.ticket_status.'.$this->event->fromStatus),
                'to' => __('user::portal.ticket_status.'.$this->event->toStatus),
            ]))
            ->action(__('user::portal.notifications.view_ticket'), route('portal.tickets.show', $ticket));
    }

    public function toArray(object $notifiable): array
    {
        $ticket = $this->event->ticket;

        return [
            'type' => 'ticket_status_changed',
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'from' => $this->event->fromStatus,
            'to' => $this->event->toStatus,
            'message' => __('user::portal.notifications.ticket_status_line', [
                'number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'from' => __('user::portal.ticket_status.'.$this->event->fromStatus),
                'to' => __('user::portal.ticket_status.'.$this->event->toStatus),
            ]),
            'url' => route('portal.tickets.show', $ticket),
        ];
    }
}

<?php

namespace Modules\Finance\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Finance\Events\InvoiceSentToCustomer;

class InvoiceSentToCustomerNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly InvoiceSentToCustomer $event) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $invoice = $this->event->invoice;

        return (new MailMessage)
            ->subject(__('user::portal.notifications.invoice_subject', ['number' => $invoice->invoice_number]))
            ->greeting(__('user::portal.notifications.greeting', ['name' => $notifiable->name]))
            ->line(__('user::portal.notifications.invoice_line', [
                'number' => $invoice->invoice_number,
                'total' => number_format((float) $invoice->total, 2).' '.$invoice->currency,
            ]))
            ->when(
                $invoice->project,
                fn (MailMessage $mail) => $mail->line(__('user::portal.notifications.invoice_project', [
                    'title' => $invoice->project->title,
                ]))
            )
            ->when(
                $invoice->project_id,
                fn (MailMessage $mail) => $mail->action(
                    __('user::portal.notifications.view_project'),
                    route('portal.projects.show', $invoice->project_id)
                )
            );
    }

    public function toArray(object $notifiable): array
    {
        $invoice = $this->event->invoice;

        return [
            'type' => 'invoice_sent',
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'project_id' => $invoice->project_id,
            'project_title' => $invoice->project?->title,
            'total' => (float) $invoice->total,
            'currency' => $invoice->currency,
            'message' => __('user::portal.notifications.invoice_line', [
                'number' => $invoice->invoice_number,
                'total' => number_format((float) $invoice->total, 2).' '.$invoice->currency,
            ]),
            'url' => $invoice->project_id
                ? route('portal.projects.show', $invoice->project_id)
                : route('portal.dashboard'),
        ];
    }
}

<?php

namespace Modules\CRM\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\CRM\Events\DealStageChanged;

class DealStageChangedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly DealStageChanged $event) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $deal = $this->event->deal;
        $from = $this->event->fromStage?->name ?? __('crm::deal.history.initial_stage');
        $to = $this->event->toStage->name;
        $changedBy = $this->event->changedBy?->name ?? __('System');

        return (new MailMessage)
            ->subject(__('crm::deal.notifications.stage_changed_subject', ['title' => $deal->title]))
            ->greeting(__('crm::deal.notifications.stage_changed_greeting'))
            ->line(__('crm::deal.notifications.stage_changed_line', [
                'title' => $deal->title,
                'from' => $from,
                'to' => $to,
            ]))
            ->line(__('crm::deal.notifications.changed_by', ['name' => $changedBy]))
            ->when(
                $deal->company,
                fn (MailMessage $mail) => $mail->line(__('crm::deal.notifications.company', ['name' => $deal->company->name]))
            )
            ->action(__('crm::deal.notifications.view_deal'), route('admin.deals.show', $deal->id));
    }
}

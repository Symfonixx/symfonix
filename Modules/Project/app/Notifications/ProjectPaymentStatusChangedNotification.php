<?php

namespace Modules\Project\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Project\Events\ProjectPaymentStatusChanged;

class ProjectPaymentStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly ProjectPaymentStatusChanged $event) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $project = $this->event->project;
        $from = __('project::project.payment_status.'.$this->event->fromStatus);
        $to = __('project::project.payment_status.'.$this->event->toStatus);

        return (new MailMessage)
            ->subject(__('user::portal.notifications.payment_changed_subject', ['title' => $project->title]))
            ->greeting(__('user::portal.notifications.greeting', ['name' => $notifiable->name]))
            ->line(__('user::portal.notifications.payment_changed_line', [
                'title' => $project->title,
                'from' => $from,
                'to' => $to,
            ]))
            ->action(__('user::portal.notifications.view_project'), route('portal.projects.show', $project));
    }

    public function toArray(object $notifiable): array
    {
        $project = $this->event->project;
        $from = __('project::project.payment_status.'.$this->event->fromStatus);
        $to = __('project::project.payment_status.'.$this->event->toStatus);

        return [
            'type' => 'project_payment_status_changed',
            'project_id' => $project->id,
            'project_title' => $project->title,
            'from' => $from,
            'to' => $to,
            'message' => __('user::portal.notifications.payment_changed_line', [
                'title' => $project->title,
                'from' => $from,
                'to' => $to,
            ]),
            'url' => route('portal.projects.show', $project),
        ];
    }
}

<?php

namespace Modules\Project\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Project\Events\ProjectStatusChanged;

class ProjectStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly ProjectStatusChanged $event) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $project = $this->event->project;
        $from = $this->event->fromStatus?->name ?? __('user::portal.notifications.initial_stage');
        $to = $this->event->toStatus->name;

        return (new MailMessage)
            ->subject(__('user::portal.notifications.status_changed_subject', ['title' => $project->title]))
            ->greeting(__('user::portal.notifications.greeting', ['name' => $notifiable->name]))
            ->line(__('user::portal.notifications.status_changed_line', [
                'title' => $project->title,
                'from' => $from,
                'to' => $to,
            ]))
            ->action(__('user::portal.notifications.view_project'), route('portal.projects.show', $project));
    }

    public function toArray(object $notifiable): array
    {
        $project = $this->event->project;
        $from = $this->event->fromStatus?->name ?? __('user::portal.notifications.initial_stage');

        return [
            'type' => 'project_status_changed',
            'project_id' => $project->id,
            'project_title' => $project->title,
            'from' => $from,
            'to' => $this->event->toStatus->name,
            'message' => __('user::portal.notifications.status_changed_line', [
                'title' => $project->title,
                'from' => $from,
                'to' => $this->event->toStatus->name,
            ]),
            'url' => route('portal.projects.show', $project),
        ];
    }
}

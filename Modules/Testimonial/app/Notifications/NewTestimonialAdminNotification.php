<?php

namespace Modules\Testimonial\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Testimonial\Events\TestimonialSubmitted;

class NewTestimonialAdminNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly TestimonialSubmitted $event) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $testimonial = $this->event->testimonial;
        $testimonial->loadMissing([
            'customer:id,name,email',
            'project:id,title',
        ]);

        return (new MailMessage)
            ->subject(__('testimonial::notifications.new_testimonial_subject'))
            ->greeting(__('testimonial::notifications.greeting', ['name' => $notifiable->name]))
            ->line(__('testimonial::notifications.new_testimonial_line', [
                'customer' => $testimonial->customer?->name ?? __('N/A'),
                'project' => $testimonial->project?->title ?? __('N/A'),
            ]))
            ->action(__('testimonial::notifications.view_testimonials'), route('admin.testimonials.index'));
    }

    public function toArray(object $notifiable): array
    {
        $testimonial = $this->event->testimonial;
        $testimonial->loadMissing([
            'customer:id,name',
            'project:id,title',
        ]);

        return [
            'type' => 'testimonial_submitted',
            'testimonial_id' => $testimonial->id,
            'customer_name' => $testimonial->customer?->name,
            'project_title' => $testimonial->project?->title,
            'message' => __('testimonial::notifications.new_testimonial_line', [
                'customer' => $testimonial->customer?->name ?? __('N/A'),
                'project' => $testimonial->project?->title ?? __('N/A'),
            ]),
            'url' => route('admin.testimonials.edit', $testimonial),
        ];
    }
}

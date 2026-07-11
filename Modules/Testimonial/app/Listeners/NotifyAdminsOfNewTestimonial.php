<?php

namespace Modules\Testimonial\Listeners;

use Illuminate\Support\Facades\Notification;
use Modules\Testimonial\Events\TestimonialSubmitted;
use Modules\Testimonial\Notifications\NewTestimonialAdminNotification;
use Modules\Testimonial\Support\TestimonialNotificationRecipients;

class NotifyAdminsOfNewTestimonial
{
    public function handle(TestimonialSubmitted $event): void
    {
        $event->testimonial->loadMissing([
            'customer:id,name,email',
            'project:id,title',
        ]);

        $recipients = TestimonialNotificationRecipients::testimonialAdmins();

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new NewTestimonialAdminNotification($event));
    }
}

<?php

namespace Modules\Testimonial\Support;

use App\Models\User;
use Illuminate\Support\Collection;

class TestimonialNotificationRecipients
{
    /**
     * @return Collection<int, User>
     */
    public static function testimonialAdmins(): Collection
    {
        return User::permission('cms.testimonials.view')->get();
    }
}

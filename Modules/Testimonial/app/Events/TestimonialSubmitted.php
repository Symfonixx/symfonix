<?php

namespace Modules\Testimonial\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Testimonial\Models\Testimonial;

class TestimonialSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Testimonial $testimonial) {}
}

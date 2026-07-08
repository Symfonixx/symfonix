<?php

namespace Modules\Testimonial\Data;

use Modules\Cms\Enums\CmsStatus;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class TestimonialData extends Data
{
    public function __construct(
        #[Required, StringType]
        public string $quote,

        #[Nullable]
        public CmsStatus $status = CmsStatus::PUBLISHED,
    ) {}
}

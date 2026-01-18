<?php

namespace Modules\Testimonial\Data;

use Illuminate\Http\UploadedFile;
use Modules\Cms\Enums\CmsStatus;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class TestimonialData extends Data
{
    public function __construct(
        #[Required, StringType, Rule('min:2', 'max:255')]
        public string $name,

        #[Nullable, StringType, Rule('max:255')]
        public ?string $position,

        #[Nullable, StringType, Rule('max:255')]
        public ?string $url,

        #[Nullable, File, Rule('mimes:jpeg,jpg,png,gif,webp', 'max:2048')]
        public ?UploadedFile $avatar,

        #[Required, StringType]
        public string $quote,

        #[Nullable]
        public CmsStatus $status = CmsStatus::PUBLISHED,
    ) {}
}

<?php

namespace Modules\Cms\Data;

use Illuminate\Http\UploadedFile;
use Modules\Cms\Enums\ServiceStatus;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class BlogData extends Data
{
    public function __construct(
        #[Required, StringType, Rule('min:2', 'max:255')]
        public string $title,

        #[Required, StringType, Rule('min:2', 'max:255')]
        public string $slug,

        #[Nullable, StringType, Rule('max:255')]
        public ?string $description,

        #[Required, StringType]
        public string $content,

        #[Nullable, StringType, Rule('max:255')]
        public ?string $keywords,

        #[Nullable, File, Rule('mimes:jpeg,jpg,png,gif,webp', 'max:2048')]
        public ?UploadedFile $image,

        #[Nullable]
        public ServiceStatus $status,

        #[Nullable, BooleanType]
        public ?bool $featured,

        #[Required]
        public int $category_id,
    ) {}
}

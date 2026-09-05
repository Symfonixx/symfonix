<?php

namespace Modules\Cms\Data;

use Illuminate\Http\UploadedFile;
use Modules\Cms\Enums\CmsStatus;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Data;

class ClientData extends Data
{
    public function __construct(
        #[Required, StringType, Rule('min:2', 'max:255')]
        public string $name,

        #[Nullable, File, Rule('mimes:jpeg,jpg,png,gif,webp', 'max:2048')]
        public ?UploadedFile $logo = null,

        #[Nullable, StringType, Url, Rule('max:500')]
        public ?string $url = null,

        #[Nullable, IntegerType, Rule('min:0')]
        public ?int $rank = 0,

        #[Nullable]
        public CmsStatus $status = CmsStatus::PUBLISHED,
    ) {}
}

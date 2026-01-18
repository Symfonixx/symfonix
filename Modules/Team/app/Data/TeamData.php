<?php

namespace Modules\Team\Data;

use Illuminate\Http\UploadedFile;
use Modules\Cms\Enums\CmsStatus;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class TeamData extends Data
{
    public function __construct(
        #[Required, StringType, Rule('min:2', 'max:255')]
        public string $name,

        #[Required, StringType, Rule('max:255')]
        public string $position,

        #[Nullable, StringType, Rule('max:255')]
        public ?string $linked_in,

        #[Nullable, StringType, Rule('max:255')]
        public ?string $facebook,

        #[Nullable, StringType, Rule('max:255')]
        public ?string $github,

        #[Nullable, StringType, Rule('max:255')]
        public ?string $behance,

        #[Nullable, File, Rule('mimes:pdf,doc,docx', 'max:5120')]
        public ?UploadedFile $resume,

        #[Nullable, StringType]
        public ?string $key_skills,

        #[Nullable, File, Rule('mimes:jpeg,jpg,png,gif,webp', 'max:2048')]
        public ?UploadedFile $avatar,

        #[Nullable]
        public CmsStatus $status = CmsStatus::PUBLISHED,
    ) {}
}

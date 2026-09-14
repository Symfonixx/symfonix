<?php

namespace Modules\User\app\Data;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class EmployeeData extends Data
{
    public function __construct(
        #[StringType, Min(3), Max(50)]
        public string $name,

        #[Email, Max(255)]
        public string $email,

        #[Regex('/^[0-9]{10,15}$/')]
        public int $mobile,

        #[Nullable, StringType, Max(120)]
        public ?string $position = null,

        #[Nullable, StringType, Max(255)]
        public ?string $resume = null,
    ) {}
}

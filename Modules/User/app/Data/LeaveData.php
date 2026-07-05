<?php

namespace Modules\User\app\Data;

use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class LeaveData extends Data
{
    public function __construct(
        #[IntegerType]
        public int $employee_id,

        #[StringType]
        public string $type,

        #[Date]
        public string $start_date,

        #[Date]
        public string $end_date,

        #[StringType]
        public string $status,

        #[Nullable, StringType, Max(1000)]
        public ?string $reason,

        #[Nullable, StringType, Max(1000)]
        public ?string $manager_note,
    ) {}
}

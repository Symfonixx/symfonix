<?php

namespace Modules\Project\DTOs\ProjectStatus;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ProjectStatusData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $name,

        #[Required, StringType, Max(7)]
        public string $color_code,

        #[Required, Min(0)]
        public int $sort_order,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            name: $payload['name'],
            color_code: $payload['color_code'],
            sort_order: (int) $payload['sort_order'],
        );
    }
}

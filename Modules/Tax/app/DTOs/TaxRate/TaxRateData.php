<?php

namespace Modules\Tax\DTOs\TaxRate;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class TaxRateData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $name,

        #[Required, Numeric, Min(0), Max(100)]
        public float $percentage,

        #[Required, StringType, In(['inclusive', 'exclusive'])]
        public string $type,

        #[Nullable, StringType, Max(10)]
        public ?string $region_code = null,

        #[Required, StringType, In(['active', 'inactive'])]
        public string $status = 'active',

        public bool $is_default = false,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            name: $payload['name'],
            percentage: (float) $payload['percentage'],
            type: $payload['type'],
            region_code: $payload['region_code'] ?? null,
            status: $payload['status'] ?? 'active',
            is_default: (bool) ($payload['is_default'] ?? false),
        );
    }
}

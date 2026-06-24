<?php

namespace Modules\CRM\DTOs\Deal;

use Modules\CRM\Models\Deal;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class DealData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $title,

        #[Nullable]
        public ?int $company_id,

        #[Required]
        public int $pipeline_stage_id,

        #[Nullable]
        public ?int $assigned_to,

        #[Nullable, Numeric, Min(0)]
        public ?float $value,

        #[Required, StringType, Max(3)]
        public string $currency = 'USD',

        #[Nullable, Min(0), Max(100)]
        public ?int $probability,

        #[Nullable, Date]
        public ?string $expected_close_date,

        #[Nullable, StringType, Max(100)]
        public ?string $source,

        #[Nullable, StringType]
        public ?string $description,

        #[Nullable, StringType]
        public ?string $lost_reason,

        #[Required, StringType, In([Deal::STATUS_OPEN, Deal::STATUS_WON, Deal::STATUS_LOST])]
        public string $status = Deal::STATUS_OPEN,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            title: $payload['title'],
            company_id: isset($payload['company_id']) ? (int) $payload['company_id'] : null,
            pipeline_stage_id: (int) $payload['pipeline_stage_id'],
            assigned_to: isset($payload['assigned_to']) ? (int) $payload['assigned_to'] : null,
            value: isset($payload['value']) ? (float) $payload['value'] : null,
            currency: $payload['currency'] ?? 'USD',
            probability: isset($payload['probability']) ? (int) $payload['probability'] : null,
            expected_close_date: $payload['expected_close_date'] ?? null,
            source: $payload['source'] ?? null,
            description: $payload['description'] ?? null,
            lost_reason: $payload['lost_reason'] ?? null,
            status: $payload['status'] ?? Deal::STATUS_OPEN,
        );
    }
}

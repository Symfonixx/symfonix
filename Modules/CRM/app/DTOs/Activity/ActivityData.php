<?php

namespace Modules\CRM\DTOs\Activity;

use Modules\CRM\Models\CrmActivity;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ActivityData extends Data
{
    public function __construct(
        #[Required, StringType, In(CrmActivity::TYPES)]
        public string $type,

        #[Nullable, StringType, Max(255)]
        public ?string $title,

        #[Nullable, StringType]
        public ?string $body,

        #[Nullable, Date]
        public ?string $scheduled_at,

        #[Nullable, Date]
        public ?string $completed_at,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            type: $payload['type'],
            title: $payload['title'] ?? null,
            body: $payload['body'] ?? null,
            scheduled_at: $payload['scheduled_at'] ?? null,
            completed_at: $payload['completed_at'] ?? null,
        );
    }
}

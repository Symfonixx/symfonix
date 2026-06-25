<?php

namespace Modules\Project\DTOs\Project;

use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ProjectData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $title,

        #[Nullable, StringType]
        public ?string $description,

        #[Required]
        public int $company_id,

        #[Required]
        public int $project_status_id,

        #[Nullable]
        public ?int $deal_id,

        #[Nullable, Numeric, Min(0)]
        public ?float $budget,

        #[Nullable, Date]
        public ?string $start_date,

        #[Nullable, Date]
        public ?string $due_date,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            title: $payload['title'],
            description: $payload['description'] ?? null,
            company_id: (int) $payload['company_id'],
            project_status_id: (int) $payload['project_status_id'],
            deal_id: isset($payload['deal_id']) ? (int) $payload['deal_id'] : null,
            budget: isset($payload['budget']) ? (float) $payload['budget'] : null,
            start_date: $payload['start_date'] ?? null,
            due_date: $payload['due_date'] ?? null,
        );
    }
}

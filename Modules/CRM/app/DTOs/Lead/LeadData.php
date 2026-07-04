<?php

namespace Modules\CRM\DTOs\Lead;

use Modules\CRM\Models\Lead;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class LeadData extends Data
{
    public function __construct(
        #[Nullable, StringType, Max(255)]
        public ?string $name,

        #[Nullable, Email, Max(255)]
        public ?string $email,

        #[Nullable, StringType, Max(50)]
        public ?string $phone,

        #[Nullable]
        public ?int $company_id,

        #[Nullable]
        public ?int $assigned_to,

        #[Nullable, StringType, Max(255)]
        public ?string $company_name,

        #[Required, StringType, In(Lead::SOURCES)]
        public string $source,

        #[Nullable, StringType, Max(255)]
        public ?string $project_budget,

        #[Nullable]
        public ?int $service_id,

        #[Nullable, StringType, Max(255)]
        public ?string $service_interest,

        #[Nullable, StringType]
        public ?string $problem_statement,

        public bool $blocked = false,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            name: $payload['name'] ?? null,
            email: $payload['email'] ?? null,
            phone: $payload['phone'] ?? null,
            company_id: isset($payload['company_id']) ? (int) $payload['company_id'] : null,
            assigned_to: isset($payload['assigned_to']) ? (int) $payload['assigned_to'] : null,
            company_name: $payload['company_name'] ?? null,
            source: $payload['source'],
            project_budget: $payload['project_budget'] ?? null,
            service_id: isset($payload['service_id']) ? (int) $payload['service_id'] : null,
            service_interest: $payload['service_interest'] ?? null,
            problem_statement: $payload['problem_statement'] ?? null,
            blocked: (bool) ($payload['blocked'] ?? false),
        );
    }
}

<?php

namespace Modules\CRM\DTOs\Contact;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ContactData extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $company_id,

        #[Nullable]
        public ?int $user_id,

        #[Required, StringType, Max(255)]
        public string $name,

        #[Nullable, Email, Max(255)]
        public ?string $email,

        #[Nullable, StringType, Max(50)]
        public ?string $phone,

        #[Nullable, StringType, Max(50)]
        public ?string $phone2,

        #[Nullable, StringType, Max(50)]
        public ?string $source,

        #[Nullable, StringType, Max(100)]
        public ?string $job_title,

        #[Nullable, StringType]
        public ?string $notes,

        public bool $is_primary = false,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            company_id: isset($payload['company_id']) && $payload['company_id'] !== '' ? (int) $payload['company_id'] : null,
            user_id: isset($payload['user_id']) && $payload['user_id'] !== '' ? (int) $payload['user_id'] : null,
            name: $payload['name'],
            email: $payload['email'] ?? null,
            phone: $payload['phone'] ?? null,
            phone2: $payload['phone2'] ?? null,
            source: $payload['source'] ?? null,
            job_title: $payload['job_title'] ?? null,
            notes: $payload['notes'] ?? null,
            is_primary: filter_var($payload['is_primary'] ?? false, FILTER_VALIDATE_BOOLEAN),
        );
    }
}

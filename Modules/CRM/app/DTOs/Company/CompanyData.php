<?php

namespace Modules\CRM\DTOs\Company;

use Modules\CRM\Models\Company;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class CompanyData extends Data
{
    public function __construct(
        #[Required]
        public int $user_id,

        #[Required, StringType, Max(255)]
        public string $name,

        #[Nullable, StringType, In(Company::ACTIVITY_TYPES)]
        public ?string $activity_type,

        #[Nullable, Email, Max(255)]
        public ?string $email,

        #[Nullable, StringType, Max(50)]
        public ?string $phone,

        #[Nullable, StringType, Max(100)]
        public ?string $country,

        #[Nullable, StringType, Max(100)]
        public ?string $city,

        #[Nullable, StringType, Max(255)]
        public ?string $address,

        #[Nullable, StringType]
        public ?string $notes,

        #[Required, StringType, In([Company::STATUS_ACTIVE, Company::STATUS_DISABLED])]
        public string $status = Company::STATUS_ACTIVE,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            user_id: (int) $payload['user_id'],
            name: $payload['name'],
            activity_type: $payload['activity_type'] ?? null,
            email: $payload['email'] ?? null,
            phone: $payload['phone'] ?? null,
            country: $payload['country'] ?? null,
            city: $payload['city'] ?? null,
            address: $payload['address'] ?? null,
            notes: $payload['notes'] ?? null,
            status: $payload['status'] ?? Company::STATUS_ACTIVE,
        );
    }
}

<?php

namespace Modules\CRM\DTOs\Subscription;

use Modules\CRM\Models\Subscription;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class SubscriptionData extends Data
{
    public function __construct(
        #[Required]
        public int $company_id,

        #[Nullable, Exists('services', 'id')]
        public ?int $service_id = null,

        #[Required, StringType, Max(255)]
        public string $name,

        #[Required, StringType, In(Subscription::STATUSES)]
        public string $status = Subscription::STATUS_ACTIVE,

        #[Required, StringType, In(Subscription::BILLING_CYCLES)]
        public string $billing_cycle = Subscription::BILLING_MONTHLY,

        #[Required, Numeric, Min(0)]
        public float $amount = 0,

        #[Required, StringType, Max(3)]
        public string $currency = 'USD',

        #[Required, Date]
        public string $starts_at,

        #[Nullable, Date]
        public ?string $ends_at,

        #[Nullable, Date]
        public ?string $renewal_at,

        #[BooleanType]
        public bool $auto_renew = true,

        #[Nullable, Date]
        public ?string $cancelled_at = null,

        #[Nullable, StringType]
        public ?string $notes,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            company_id: (int) $payload['company_id'],
            service_id: isset($payload['service_id']) && $payload['service_id'] !== ''
                ? (int) $payload['service_id']
                : null,
            name: $payload['name'],
            status: $payload['status'] ?? Subscription::STATUS_ACTIVE,
            billing_cycle: $payload['billing_cycle'] ?? Subscription::BILLING_MONTHLY,
            amount: isset($payload['amount']) ? (float) $payload['amount'] : 0,
            currency: $payload['currency'] ?? 'USD',
            starts_at: $payload['starts_at'],
            ends_at: $payload['ends_at'] ?? null,
            renewal_at: $payload['renewal_at'] ?? null,
            auto_renew: filter_var($payload['auto_renew'] ?? true, FILTER_VALIDATE_BOOLEAN),
            cancelled_at: $payload['cancelled_at'] ?? null,
            notes: $payload['notes'] ?? null,
        );
    }
}

<?php

namespace Modules\Finance\DTOs\ExpenseCategory;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ExpenseCategoryData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $name,

        #[Nullable, StringType, Max(255)]
        public ?string $slug = null,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            name: $payload['name'],
            slug: $payload['slug'] ?? null,
        );
    }
}

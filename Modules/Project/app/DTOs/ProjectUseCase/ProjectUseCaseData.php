<?php

namespace Modules\Project\DTOs\ProjectUseCase;

use Illuminate\Http\UploadedFile;
use Modules\Cms\Enums\CmsStatus;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ProjectUseCaseData extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $project_id,

        #[Required, StringType, Max(255)]
        public string $title,

        #[Required, StringType, Max(255)]
        public string $slug,

        #[Nullable, StringType, Max(255)]
        public ?string $client_name,

        #[Nullable, StringType, Max(500)]
        public ?string $summary,

        #[Nullable, StringType]
        public ?string $challenge,

        #[Nullable, StringType]
        public ?string $solution,

        #[Nullable, StringType]
        public ?string $results,

        #[Nullable, StringType]
        public ?string $content,

        #[Nullable]
        public ?UploadedFile $image,

        #[Nullable, StringType]
        public ?string $technologies,

        #[Nullable, StringType, Max(100)]
        public ?string $category_tag,

        #[Nullable, StringType, Max(255)]
        public ?string $project_url,

        #[Nullable, Min(2000), Max(2100)]
        public ?int $completed_year,

        public bool $featured = false,

        public CmsStatus $status = CmsStatus::ARCHIVED,

        #[Min(0)]
        public int $sort_order = 0,

        public bool $auto_translate = false,
    ) {}

    public static function fromRequest(array $payload): self
    {
        return new self(
            project_id: isset($payload['project_id']) ? (int) $payload['project_id'] : null,
            title: $payload['title'],
            slug: $payload['slug'],
            client_name: $payload['client_name'] ?? null,
            summary: $payload['summary'] ?? null,
            challenge: $payload['challenge'] ?? null,
            solution: $payload['solution'] ?? null,
            results: $payload['results'] ?? null,
            content: $payload['content'] ?? null,
            image: $payload['image'] ?? null,
            technologies: $payload['technologies'] ?? null,
            category_tag: $payload['category_tag'] ?? null,
            project_url: $payload['project_url'] ?? null,
            completed_year: isset($payload['completed_year']) ? (int) $payload['completed_year'] : null,
            featured: (bool) ($payload['featured'] ?? false),
            status: $payload['status'] instanceof CmsStatus
                ? $payload['status']
                : CmsStatus::from($payload['status'] ?? CmsStatus::ARCHIVED->value),
            sort_order: (int) ($payload['sort_order'] ?? 0),
            auto_translate: wantsAutoTranslate($payload),
        );
    }
}

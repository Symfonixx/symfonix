<?php

namespace Modules\Project\Repositories\ProjectUseCase;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Cms\Enums\CmsStatus;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Core\Traits\FileTrait;
use Modules\Project\DTOs\ProjectUseCase\ProjectUseCaseData;
use Modules\Project\Models\ProjectUseCase;

class ProjectUseCaseModelRepository implements ProjectUseCaseRepository
{
    use ExceptionHandlerTrait, FileTrait;

    private string $uploadPath = 'project-use-cases';

    public function paginateAdmin(int $perPage = 15): LengthAwarePaginator
    {
        return ProjectUseCase::query()
            ->with('project:id,title')
            ->ordered()
            ->paginate($perPage);
    }

    public function publishedPaginate(int $perPage = 9): LengthAwarePaginator
    {
        return ProjectUseCase::query()
            ->published()
            ->ordered()
            ->paginate($perPage);
    }

    public function featured(int $limit = 6): Collection
    {
        return ProjectUseCase::query()
            ->published()
            ->featured()
            ->ordered()
            ->limit($limit)
            ->get();
    }

    public function findBySlug(string $slug): ProjectUseCase
    {
        return ProjectUseCase::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function findOrFail(int $id): ProjectUseCase
    {
        return ProjectUseCase::query()->findOrFail($id);
    }

    public function create(ProjectUseCaseData $data): ?ProjectUseCase
    {
        return $this->execute(function () use ($data) {
            $useCase = ProjectUseCase::create($this->prepareData($data));
            session()->flushMessage(true);

            return $useCase;
        });
    }

    public function update(ProjectUseCase $useCase, ProjectUseCaseData $data): ?ProjectUseCase
    {
        return $this->execute(function () use ($useCase, $data) {
            $useCase->update($this->prepareUpdateData($data, $useCase));
            session()->flushMessage(true);

            return $useCase;
        });
    }

    public function delete(ProjectUseCase $useCase): ?bool
    {
        return $this->execute(function () use ($useCase) {
            if ($useCase->image) {
                $this->deleteFile($useCase->image);
            }

            $deleted = $useCase->delete();
            session()->flushMessage(true);

            return $deleted;
        });
    }

    public function bulkDelete(array $ids): ?bool
    {
        return $this->execute(function () use ($ids) {
            $images = ProjectUseCase::query()->whereIn('id', $ids)->pluck('image')->filter()->toArray();
            ProjectUseCase::destroy($ids);
            $this->deleteFile($images);
            session()->flushMessage(true);

            return true;
        });
    }

    private function prepareData(ProjectUseCaseData $data): array
    {
        $translations = buildTranslations([
            'title' => $data->title,
            'client_name' => $data->client_name ?? '',
            'summary' => $data->summary ?? '',
            'challenge' => $data->challenge ?? '',
            'solution' => $data->solution ?? '',
            'results' => $data->results ?? '',
            'content' => $data->content ?? '',
        ], $data->auto_translate);

        return array_merge($translations, [
            'project_id' => $data->project_id,
            'slug' => $data->slug,
            'image' => $this->handleImageUpload($data),
            'technologies' => $this->parseTechnologies($data->technologies),
            'category_tag' => $data->category_tag,
            'project_url' => $data->project_url,
            'completed_year' => $data->completed_year,
            'featured' => $data->featured,
            'status' => $data->status->value,
            'sort_order' => $data->sort_order,
        ]);
    }

    private function prepareUpdateData(ProjectUseCaseData $data, ProjectUseCase $useCase): array
    {
        $translations = buildTranslations([
            'title' => $data->title,
            'client_name' => $data->client_name ?? '',
            'summary' => $data->summary ?? '',
            'challenge' => $data->challenge ?? '',
            'solution' => $data->solution ?? '',
            'results' => $data->results ?? '',
            'content' => $data->content ?? '',
        ], $data->auto_translate, [
            'title' => $useCase->getTranslations('title'),
            'client_name' => $useCase->getTranslations('client_name'),
            'summary' => $useCase->getTranslations('summary'),
            'challenge' => $useCase->getTranslations('challenge'),
            'solution' => $useCase->getTranslations('solution'),
            'results' => $useCase->getTranslations('results'),
            'content' => $useCase->getTranslations('content'),
        ]);

        return array_merge($translations, [
            'project_id' => $data->project_id,
            'slug' => $data->slug,
            'image' => $this->handleImageUpload($data, $useCase->image),
            'technologies' => $this->parseTechnologies($data->technologies),
            'category_tag' => $data->category_tag,
            'project_url' => $data->project_url,
            'completed_year' => $data->completed_year,
            'featured' => $data->featured,
            'status' => $data->status->value,
            'sort_order' => $data->sort_order,
        ]);
    }

    private function handleImageUpload(ProjectUseCaseData $data, ?string $existing = null): ?string
    {
        if ($data->image) {
            return $this->upload($data->image, $this->uploadPath, null, $existing);
        }

        return $existing;
    }

    private function parseTechnologies(mixed $value): ?array
    {
        if (is_array($value)) {
            return array_values(array_filter($value));
        }

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter(array_map(
                fn ($item) => is_array($item) ? ($item['value'] ?? null) : $item,
                $decoded
            )));
        }

        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }
}

<?php

namespace Modules\Project\Repositories\ProjectUseCase;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
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
        return [
            'project_id' => $data->project_id,
            'slug' => $data->slug,
            'title' => $this->translateOnCreate($data->title),
            'client_name' => $this->translateOnCreate($data->client_name ?? ''),
            'summary' => $this->translateOnCreate($data->summary ?? ''),
            'challenge' => $this->translateOnCreate($data->challenge ?? ''),
            'solution' => $this->translateOnCreate($data->solution ?? ''),
            'results' => $this->translateOnCreate($data->results ?? ''),
            'content' => $this->translateOnCreate($data->content ?? ''),
            'image' => $this->handleImageUpload($data),
            'technologies' => $this->parseTechnologies($data->technologies),
            'category_tag' => $data->category_tag,
            'project_url' => $data->project_url,
            'completed_year' => $data->completed_year,
            'featured' => $data->featured,
            'status' => $data->status->value,
            'sort_order' => $data->sort_order,
        ];
    }

    private function prepareUpdateData(ProjectUseCaseData $data, ProjectUseCase $useCase): array
    {
        return [
            'project_id' => $data->project_id,
            'slug' => $data->slug,
            'title' => $this->mergeTranslation($useCase, 'title', $data->title),
            'client_name' => $this->mergeTranslation($useCase, 'client_name', $data->client_name ?? ''),
            'summary' => $this->mergeTranslation($useCase, 'summary', $data->summary ?? ''),
            'challenge' => $this->mergeTranslation($useCase, 'challenge', $data->challenge ?? ''),
            'solution' => $this->mergeTranslation($useCase, 'solution', $data->solution ?? ''),
            'results' => $this->mergeTranslation($useCase, 'results', $data->results ?? ''),
            'content' => $this->mergeTranslation($useCase, 'content', $data->content ?? ''),
            'image' => $this->handleImageUpload($data, $useCase->image),
            'technologies' => $this->parseTechnologies($data->technologies),
            'category_tag' => $data->category_tag,
            'project_url' => $data->project_url,
            'completed_year' => $data->completed_year,
            'featured' => $data->featured,
            'status' => $data->status->value,
            'sort_order' => $data->sort_order,
        ];
    }

    private function translateOnCreate(string $value): array
    {
        $locale = app()->getLocale();
        $translations = [$locale => $value];

        foreach (otherLangs() as $lang) {
            try {
                $translations[$lang] = autoGoogleTranslator($lang, $value);
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }

        return $translations;
    }

    private function mergeTranslation(ProjectUseCase $useCase, string $field, string $value): array
    {
        $translations = $useCase->getTranslations($field);
        $translations[app()->getLocale()] = $value;

        return $translations;
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

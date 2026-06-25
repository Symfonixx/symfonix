<?php

namespace Modules\Project\Services\ProjectUseCase;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Modules\Project\DTOs\ProjectUseCase\ProjectUseCaseData;
use Modules\Project\Models\ProjectUseCase;
use Modules\Project\Repositories\ProjectUseCase\ProjectUseCaseRepository;

class ProjectUseCaseService
{
    public function __construct(private readonly ProjectUseCaseRepository $repository) {}

    public function listAdmin(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateAdmin($perPage);
    }

    public function create(ProjectUseCaseData $data): ?ProjectUseCase
    {
        $useCase = $this->repository->create($data);

        if ($useCase) {
            Log::info('Project use case created', [
                'use_case_id' => $useCase->id,
                'slug' => $useCase->slug,
                'actor_id' => auth()->id(),
            ]);
        }

        return $useCase;
    }

    public function update(ProjectUseCase $useCase, ProjectUseCaseData $data): ?ProjectUseCase
    {
        $updated = $this->repository->update($useCase, $data);

        if ($updated) {
            Log::info('Project use case updated', [
                'use_case_id' => $useCase->id,
                'slug' => $useCase->slug,
                'actor_id' => auth()->id(),
            ]);
        }

        return $updated;
    }

    public function delete(ProjectUseCase $useCase): ?bool
    {
        $deleted = $this->repository->delete($useCase);

        if ($deleted) {
            Log::warning('Project use case deleted', [
                'use_case_id' => $useCase->id,
                'slug' => $useCase->slug,
                'actor_id' => auth()->id(),
            ]);
        }

        return $deleted;
    }

    public function bulkDelete(array $ids): ?bool
    {
        $deleted = $this->repository->bulkDelete($ids);

        if ($deleted) {
            Log::warning('Project use cases bulk deleted', [
                'use_case_ids' => $ids,
                'actor_id' => auth()->id(),
            ]);
        }

        return $deleted;
    }
}

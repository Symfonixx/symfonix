<?php

namespace Modules\Project\Repositories\ProjectUseCase;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Project\DTOs\ProjectUseCase\ProjectUseCaseData;
use Modules\Project\Models\ProjectUseCase;

interface ProjectUseCaseRepository
{
    public function paginateAdmin(int $perPage = 15): LengthAwarePaginator;

    public function publishedPaginate(int $perPage = 9): LengthAwarePaginator;

    public function featured(int $limit = 6): Collection;

    public function findBySlug(string $slug): ProjectUseCase;

    public function findOrFail(int $id): ProjectUseCase;

    public function create(ProjectUseCaseData $data): ?ProjectUseCase;

    public function update(ProjectUseCase $useCase, ProjectUseCaseData $data): ?ProjectUseCase;

    public function delete(ProjectUseCase $useCase): ?bool;

    public function bulkDelete(array $ids): ?bool;
}

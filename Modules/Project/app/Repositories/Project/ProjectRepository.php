<?php

namespace Modules\Project\Repositories\Project;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Project\DTOs\Project\ProjectData;
use Modules\Project\Models\Project;

interface ProjectRepository
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findOrFail(int $id): Project;

    public function create(ProjectData $data): ?Project;

    public function update(Project $project, ProjectData $data): ?Project;

    public function delete(Project $project): ?bool;

    public function bulkDelete(array $ids): ?bool;
}

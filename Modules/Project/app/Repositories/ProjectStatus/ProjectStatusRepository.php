<?php

namespace Modules\Project\Repositories\ProjectStatus;

use Illuminate\Support\Collection;
use Modules\Project\DTOs\ProjectStatus\ProjectStatusData;
use Modules\Project\Models\ProjectStatus;

interface ProjectStatusRepository
{
    public function allOrdered(): Collection;

    public function findOrFail(int $id): ProjectStatus;

    public function create(ProjectStatusData $data): ?ProjectStatus;

    public function update(ProjectStatus $status, ProjectStatusData $data): ?ProjectStatus;

    public function delete(ProjectStatus $status): ?bool;
}

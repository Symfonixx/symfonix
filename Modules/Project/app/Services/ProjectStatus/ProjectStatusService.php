<?php

namespace Modules\Project\Services\ProjectStatus;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Project\DTOs\ProjectStatus\ProjectStatusData;
use Modules\Project\Models\ProjectStatus;
use Modules\Project\Repositories\ProjectStatus\ProjectStatusRepository;

class ProjectStatusService
{
    public function __construct(private readonly ProjectStatusRepository $repository) {}

    public function list(): Collection
    {
        return ProjectStatus::query()->withCount('projects')->orderBy('sort_order')->get();
    }

    public function create(ProjectStatusData $data): ?ProjectStatus
    {
        $status = $this->repository->create($data);

        if ($status) {
            Log::info('Project status created', [
                'status_id' => $status->id,
                'name' => $status->name,
                'actor_id' => auth()->id(),
            ]);
        }

        return $status;
    }

    public function update(ProjectStatus $status, ProjectStatusData $data): ?ProjectStatus
    {
        $updated = $this->repository->update($status, $data);

        if ($updated) {
            Log::info('Project status updated', [
                'status_id' => $status->id,
                'name' => $status->name,
                'actor_id' => auth()->id(),
            ]);
        }

        return $updated;
    }

    public function delete(ProjectStatus $status): ?bool
    {
        $deleted = $this->repository->delete($status);

        if ($deleted) {
            Log::warning('Project status deleted', [
                'status_id' => $status->id,
                'name' => $status->name,
                'actor_id' => auth()->id(),
            ]);
        }

        return $deleted;
    }
}

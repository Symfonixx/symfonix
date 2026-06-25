<?php

namespace Modules\Project\Services\Project;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Modules\Project\DTOs\Project\ProjectData;
use Modules\Project\Models\Project;
use Modules\Project\Repositories\Project\ProjectRepository;

class ProjectService
{
    public function __construct(private readonly ProjectRepository $repository) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, (int) config('core.page_size', 15));
    }

    public function create(ProjectData $data): ?Project
    {
        $project = $this->repository->create($data);

        if ($project) {
            Log::info('Project created', [
                'project_id' => $project->id,
                'title' => $project->title,
                'actor_id' => auth()->id(),
            ]);
        }

        return $project;
    }

    public function update(Project $project, ProjectData $data): ?Project
    {
        $updated = $this->repository->update($project, $data);

        if ($updated) {
            Log::info('Project updated', [
                'project_id' => $project->id,
                'title' => $project->title,
                'actor_id' => auth()->id(),
            ]);
        }

        return $updated;
    }

    public function delete(Project $project): ?bool
    {
        $deleted = $this->repository->delete($project);

        if ($deleted) {
            Log::warning('Project deleted', [
                'project_id' => $project->id,
                'title' => $project->title,
                'actor_id' => auth()->id(),
            ]);
        }

        return $deleted;
    }

    public function bulkDelete(array $ids): ?bool
    {
        $deleted = $this->repository->bulkDelete($ids);

        if ($deleted) {
            Log::warning('Projects bulk deleted', [
                'project_ids' => $ids,
                'actor_id' => auth()->id(),
            ]);
        }

        return $deleted;
    }
}

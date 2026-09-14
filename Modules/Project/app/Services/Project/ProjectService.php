<?php

namespace Modules\Project\Services\Project;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Project\DTOs\Project\ProjectData;
use Modules\Project\Events\ProjectStatusChanged;
use Modules\Project\Models\Project;
use Modules\Project\Models\ProjectStatus;
use Modules\Project\Repositories\Project\ProjectRepository;

class ProjectService
{
    public function __construct(private readonly ProjectRepository $repository) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, (int) config('core.page_size', 15));
    }

    public function storeAttachments(Project $project, array $files): void
    {
        if ($files === []) {
            return;
        }

        $stored = $project->attachments ?? [];

        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $path = $file->store('projects/attachments', 'public');
            $stored[] = [
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ];
        }

        if ($stored !== ($project->attachments ?? [])) {
            $project->update(['attachments' => $stored]);
        }
    }

    public function syncServices(Project $project, array $serviceIds): void
    {
        $ids = collect($serviceIds)
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $project->services()->sync($ids);
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
        $project->loadMissing('status');
        $previousStatusId = $project->project_status_id;
        $fromStatus = $project->status;

        $updated = $this->repository->update($project, $data);

        if ($updated && $previousStatusId !== $project->project_status_id) {
            $project->load('status');
            $toStatus = $project->status;

            if ($toStatus instanceof ProjectStatus) {
                ProjectStatusChanged::dispatch($project, $fromStatus, $toStatus);
            }

            Log::info('Project status changed', [
                'project_id' => $project->id,
                'from_status_id' => $previousStatusId,
                'to_status_id' => $project->project_status_id,
                'actor_id' => auth()->id(),
            ]);
        }

        if ($updated) {
            Log::info('Project updated', [
                'project_id' => $project->id,
                'title' => $project->title,
                'actor_id' => auth()->id(),
            ]);
        }

        return $updated;
    }

    public function updateStatus(Project $project, int $statusId): Project
    {
        $project->loadMissing('status');
        $previousStatusId = $project->project_status_id;

        if ($previousStatusId === $statusId) {
            return $project;
        }

        $fromStatus = $project->status;
        $project->update(['project_status_id' => $statusId]);
        $project->load('status');
        $toStatus = $project->status;

        if ($toStatus instanceof ProjectStatus) {
            ProjectStatusChanged::dispatch($project, $fromStatus, $toStatus);
        }

        Log::info('Project status changed', [
            'project_id' => $project->id,
            'from_status_id' => $previousStatusId,
            'to_status_id' => $project->project_status_id,
            'actor_id' => auth()->id(),
        ]);

        return $project;
    }

    public function delete(Project $project): ?bool
    {
        $this->deleteAttachments($project);

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

    private function deleteAttachments(Project $project): void
    {
        foreach ($project->attachments ?? [] as $attachment) {
            if (! empty($attachment['path'])) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }
    }
}

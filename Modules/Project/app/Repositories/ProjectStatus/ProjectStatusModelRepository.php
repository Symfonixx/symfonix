<?php

namespace Modules\Project\Repositories\ProjectStatus;

use Illuminate\Support\Collection;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Project\DTOs\ProjectStatus\ProjectStatusData;
use Modules\Project\Models\ProjectStatus;

class ProjectStatusModelRepository implements ProjectStatusRepository
{
    use ExceptionHandlerTrait;

    public function allOrdered(): Collection
    {
        return ProjectStatus::query()->orderBy('sort_order')->get();
    }

    public function findOrFail(int $id): ProjectStatus
    {
        return ProjectStatus::query()->findOrFail($id);
    }

    public function create(ProjectStatusData $data): ?ProjectStatus
    {
        return $this->execute(function () use ($data) {
            $status = ProjectStatus::create($data->toArray());
            session()->flushMessage(true);

            return $status;
        });
    }

    public function update(ProjectStatus $status, ProjectStatusData $data): ?ProjectStatus
    {
        return $this->execute(function () use ($status, $data) {
            $status->update($data->toArray());
            session()->flushMessage(true);

            return $status;
        });
    }

    public function delete(ProjectStatus $status): ?bool
    {
        return $this->execute(function () use ($status) {
            if ($status->projects()->exists()) {
                session()->flushMessage(false, __('project::status.errors.in_use'));

                return false;
            }

            $deleted = $status->delete();
            session()->flushMessage(true);

            return $deleted;
        });
    }
}

<?php

namespace Modules\Project\Policies;

use App\Models\User;
use Modules\Project\Models\ProjectStatus;

class ProjectStatusPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('project.statuses.view');
    }

    public function create(User $user): bool
    {
        return $user->can('project.statuses.create');
    }

    public function update(User $user, ProjectStatus $projectStatus): bool
    {
        return $user->can('project.statuses.edit');
    }

    public function delete(User $user, ProjectStatus $projectStatus): bool
    {
        return $user->can('project.statuses.delete');
    }
}

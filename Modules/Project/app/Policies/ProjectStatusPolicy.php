<?php

namespace Modules\Project\Policies;

use App\Models\User;
use Modules\Project\Models\ProjectStatus;

class ProjectStatusPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('Project Management');
    }

    public function create(User $user): bool
    {
        return $user->can('Project Management');
    }

    public function update(User $user, ProjectStatus $projectStatus): bool
    {
        return $user->can('Project Management');
    }

    public function delete(User $user, ProjectStatus $projectStatus): bool
    {
        return $user->can('Project Management');
    }
}

<?php

namespace Modules\Project\Policies;

use App\Models\User;
use Modules\Project\Models\Project;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('Project Management');
    }

    public function view(User $user, Project $project): bool
    {
        return $user->can('Project Management');
    }

    public function create(User $user): bool
    {
        return $user->can('Project Management');
    }

    public function update(User $user, Project $project): bool
    {
        return $user->can('Project Management');
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->can('Project Management');
    }
}

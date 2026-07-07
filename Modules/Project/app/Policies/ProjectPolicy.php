<?php

namespace Modules\Project\Policies;

use App\Models\User;
use Modules\Project\Models\Project;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->isCustomer()) {
            return true;
        }

        return $user->can('Project Management');
    }

    public function view(User $user, Project $project): bool
    {
        if ($this->ownsProject($user, $project)) {
            return true;
        }

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

    private function ownsProject(User $user, Project $project): bool
    {
        if (! $user->isCustomer()) {
            return false;
        }

        return in_array($project->company_id, $user->companyIds(), true);
    }
}

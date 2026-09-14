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

        return $user->can('project.projects.view');
    }

    public function view(User $user, Project $project): bool
    {
        if ($this->ownsProject($user, $project)) {
            return true;
        }

        return $user->can('project.projects.view');
    }

    public function create(User $user): bool
    {
        return $user->can('project.projects.create');
    }

    public function update(User $user, Project $project): bool
    {
        return $user->can('project.projects.edit');
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->can('project.projects.delete');
    }

    private function ownsProject(User $user, Project $project): bool
    {
        if (! $user->isCustomer()) {
            return false;
        }

        return in_array($project->company_id, $user->companyIds(), true);
    }
}

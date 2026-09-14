<?php

namespace Modules\Project\Policies;

use App\Models\User;
use Modules\Project\Models\ProjectUseCase;

class ProjectUseCasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('project.use_cases.view');
    }

    public function create(User $user): bool
    {
        return $user->can('project.use_cases.create');
    }

    public function update(User $user, ProjectUseCase $projectUseCase): bool
    {
        return $user->can('project.use_cases.edit');
    }

    public function delete(User $user, ProjectUseCase $projectUseCase): bool
    {
        return $user->can('project.use_cases.delete');
    }
}

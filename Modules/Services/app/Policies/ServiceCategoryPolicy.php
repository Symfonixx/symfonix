<?php

namespace Modules\Services\Policies;

use App\Models\User;
use Modules\Services\Models\ServiceCategory;

class ServiceCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('services.categories.view');
    }

    public function view(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->can('services.categories.view');
    }

    public function create(User $user): bool
    {
        return $user->can('services.categories.create');
    }

    public function update(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->can('services.categories.edit');
    }

    public function delete(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->can('services.categories.delete');
    }
}

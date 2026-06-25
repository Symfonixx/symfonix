<?php

namespace Modules\Services\Policies;

use App\Models\User;
use Modules\Services\Models\ServiceCategory;

class ServiceCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('Services Management');
    }

    public function view(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->can('Services Management');
    }

    public function create(User $user): bool
    {
        return $user->can('Services Management');
    }

    public function update(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->can('Services Management');
    }

    public function delete(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->can('Services Management');
    }
}

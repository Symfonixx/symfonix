<?php

namespace Modules\Services\Policies;

use App\Models\User;
use Modules\Services\Models\Service;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('services.catalog.view');
    }

    public function view(User $user, Service $service): bool
    {
        return $user->can('services.catalog.view');
    }

    public function create(User $user): bool
    {
        return $user->can('services.catalog.create');
    }

    public function update(User $user, Service $service): bool
    {
        return $user->can('services.catalog.edit');
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->can('services.catalog.delete');
    }
}

<?php

namespace Modules\Services\Policies;

use App\Models\User;
use Modules\Services\Models\Service;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('Services Management');
    }

    public function view(User $user, Service $service): bool
    {
        return $user->can('Services Management');
    }

    public function create(User $user): bool
    {
        return $user->can('Services Management');
    }

    public function update(User $user, Service $service): bool
    {
        return $user->can('Services Management');
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->can('Services Management');
    }
}

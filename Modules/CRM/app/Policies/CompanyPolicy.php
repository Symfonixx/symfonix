<?php

namespace Modules\CRM\Policies;

use App\Models\User;
use Modules\CRM\Models\Company;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('CRM Management');
    }

    public function view(User $user, Company $company): bool
    {
        return $user->can('CRM Management');
    }

    public function create(User $user): bool
    {
        return $user->can('CRM Management');
    }

    public function update(User $user, Company $company): bool
    {
        return $user->can('CRM Management');
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->can('CRM Management');
    }
}

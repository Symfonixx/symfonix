<?php

namespace Modules\CRM\Policies;

use App\Models\User;
use Modules\CRM\Models\Company;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('crm.companies.view');
    }

    public function view(User $user, Company $company): bool
    {
        return $user->can('crm.companies.view');
    }

    public function create(User $user): bool
    {
        return $user->can('crm.companies.create');
    }

    public function update(User $user, Company $company): bool
    {
        return $user->can('crm.companies.edit');
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->can('crm.companies.delete');
    }
}

<?php

namespace Modules\Finance\Policies;

use App\Models\User;
use Modules\Finance\Models\Salary;

class SalaryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('Finance Management');
    }

    public function create(User $user): bool
    {
        return $user->can('Finance Management');
    }

    public function update(User $user, Salary $salary): bool
    {
        return $user->can('Finance Management');
    }

    public function delete(User $user, Salary $salary): bool
    {
        return $user->can('Finance Management');
    }
}

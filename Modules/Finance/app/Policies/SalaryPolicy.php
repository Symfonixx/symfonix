<?php

namespace Modules\Finance\Policies;

use App\Models\User;
use Modules\Finance\Models\Salary;

class SalaryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('finance.salaries.view');
    }

    public function create(User $user): bool
    {
        return $user->can('finance.salaries.create');
    }

    public function update(User $user, Salary $salary): bool
    {
        return $user->can('finance.salaries.edit');
    }

    public function delete(User $user, Salary $salary): bool
    {
        return $user->can('finance.salaries.delete');
    }
}

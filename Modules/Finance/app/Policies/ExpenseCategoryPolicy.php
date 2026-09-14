<?php

namespace Modules\Finance\Policies;

use App\Models\User;
use Modules\Finance\Models\ExpenseCategory;

class ExpenseCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('finance.expense_categories.view');
    }

    public function create(User $user): bool
    {
        return $user->can('finance.expense_categories.create');
    }

    public function update(User $user, ExpenseCategory $expenseCategory): bool
    {
        return $user->can('finance.expense_categories.edit');
    }

    public function delete(User $user, ExpenseCategory $expenseCategory): bool
    {
        return $user->can('finance.expense_categories.delete');
    }
}

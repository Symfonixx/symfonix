<?php

namespace Modules\Finance\Repositories\ExpenseCategory;

use Illuminate\Support\Collection;
use Modules\Finance\DTOs\ExpenseCategory\ExpenseCategoryData;
use Modules\Finance\Models\ExpenseCategory;

interface ExpenseCategoryRepository
{
    public function allOrdered(): Collection;

    public function create(ExpenseCategoryData $data): ?ExpenseCategory;

    public function update(ExpenseCategory $category, ExpenseCategoryData $data): ?ExpenseCategory;

    public function delete(ExpenseCategory $category): ?bool;
}

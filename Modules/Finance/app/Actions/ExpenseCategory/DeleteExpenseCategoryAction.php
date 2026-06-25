<?php

namespace Modules\Finance\Actions\ExpenseCategory;

use Modules\Finance\Models\ExpenseCategory;
use Modules\Finance\Services\ExpenseCategory\ExpenseCategoryService;

class DeleteExpenseCategoryAction
{
    public function __construct(private readonly ExpenseCategoryService $service) {}

    public function execute(ExpenseCategory $category): ?bool
    {
        return $this->service->delete($category);
    }
}

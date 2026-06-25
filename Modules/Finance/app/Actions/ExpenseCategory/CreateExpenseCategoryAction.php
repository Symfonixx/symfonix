<?php

namespace Modules\Finance\Actions\ExpenseCategory;

use Modules\Finance\DTOs\ExpenseCategory\ExpenseCategoryData;
use Modules\Finance\Models\ExpenseCategory;
use Modules\Finance\Services\ExpenseCategory\ExpenseCategoryService;

class CreateExpenseCategoryAction
{
    public function __construct(private readonly ExpenseCategoryService $service) {}

    public function execute(ExpenseCategoryData $data): ?ExpenseCategory
    {
        return $this->service->create($data);
    }
}

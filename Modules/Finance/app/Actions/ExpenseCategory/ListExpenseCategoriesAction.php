<?php

namespace Modules\Finance\Actions\ExpenseCategory;

use Illuminate\Support\Collection;
use Modules\Finance\Services\ExpenseCategory\ExpenseCategoryService;

class ListExpenseCategoriesAction
{
    public function __construct(private readonly ExpenseCategoryService $service) {}

    public function execute(): Collection
    {
        return $this->service->list();
    }
}

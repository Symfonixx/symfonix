<?php

namespace Modules\Finance\Repositories\ExpenseCategory;

use Illuminate\Support\Collection;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Finance\DTOs\ExpenseCategory\ExpenseCategoryData;
use Modules\Finance\Models\ExpenseCategory;

class ExpenseCategoryModelRepository implements ExpenseCategoryRepository
{
    use ExceptionHandlerTrait;

    public function allOrdered(): Collection
    {
        return ExpenseCategory::query()->orderBy('name')->get();
    }

    public function create(ExpenseCategoryData $data): ?ExpenseCategory
    {
        return $this->execute(function () use ($data) {
            $category = ExpenseCategory::query()->create($data->toArray());
            session()->flushMessage(true);

            return $category;
        });
    }

    public function update(ExpenseCategory $category, ExpenseCategoryData $data): ?ExpenseCategory
    {
        return $this->execute(function () use ($category, $data) {
            $category->update($data->toArray());
            session()->flushMessage(true);

            return $category;
        });
    }

    public function delete(ExpenseCategory $category): ?bool
    {
        return $this->execute(function () use ($category) {
            if ($category->journalLines()->exists()) {
                session()->flushMessage(false, __('finance::expense_category.errors.in_use'));

                return false;
            }

            $deleted = $category->delete();
            session()->flushMessage(true);

            return $deleted;
        });
    }
}

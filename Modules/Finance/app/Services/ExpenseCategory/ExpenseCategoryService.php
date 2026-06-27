<?php

namespace Modules\Finance\Services\ExpenseCategory;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Finance\DTOs\ExpenseCategory\ExpenseCategoryData;
use Modules\Finance\Models\ExpenseCategory;
use Modules\Finance\Repositories\ExpenseCategory\ExpenseCategoryRepository;

class ExpenseCategoryService
{
    public function __construct(private readonly ExpenseCategoryRepository $repository) {}

    public function list(): Collection
    {
        return ExpenseCategory::query()
            ->withCount('journalLines')
            ->orderBy('name')
            ->get();
    }

    public function create(ExpenseCategoryData $data): ?ExpenseCategory
    {
        $category = $this->repository->create($data);

        if ($category) {
            Log::info('Finance expense category created', [
                'category_id' => $category->id,
                'name' => $category->name,
                'actor_id' => auth()->id(),
            ]);
        }

        return $category;
    }

    public function update(ExpenseCategory $category, ExpenseCategoryData $data): ?ExpenseCategory
    {
        $updated = $this->repository->update($category, $data);

        if ($updated) {
            Log::info('Finance expense category updated', [
                'category_id' => $category->id,
                'name' => $category->name,
                'actor_id' => auth()->id(),
            ]);
        }

        return $updated;
    }

    public function delete(ExpenseCategory $category): ?bool
    {
        $deleted = $this->repository->delete($category);

        if ($deleted) {
            Log::warning('Finance expense category deleted', [
                'category_id' => $category->id,
                'name' => $category->name,
                'actor_id' => auth()->id(),
            ]);
        }

        return $deleted;
    }
}

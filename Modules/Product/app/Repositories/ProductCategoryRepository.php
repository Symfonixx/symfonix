<?php

namespace Modules\Product\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Product\Models\ProductCategory;

class ProductCategoryRepository
{
    use ExceptionHandlerTrait;

    public function allOrdered(): Collection
    {
        return ProductCategory::query()->orderBy('name')->get();
    }

    public function store(array $data): ?ProductCategory
    {
        return $this->execute(function () use ($data) {
            $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

            $category = ProductCategory::query()->create($data);
            session()->flushMessage(true);

            return $category;
        });
    }

    public function update(ProductCategory $category, array $data): ?ProductCategory
    {
        return $this->execute(function () use ($category, $data) {
            if (empty($data['slug']) && ! empty($data['name'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            $category->update($data);
            session()->flushMessage(true);

            return $category;
        });
    }

    public function delete(ProductCategory $category): ?bool
    {
        return $this->execute(function () use ($category) {
            if ($category->products()->exists()) {
                session()->flushMessage(false, __('product::category.errors.in_use'));

                return false;
            }

            $deleted = $category->delete();
            session()->flushMessage(true);

            return $deleted;
        });
    }
}

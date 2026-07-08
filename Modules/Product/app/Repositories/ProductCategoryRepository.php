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
        $locale = app()->getLocale();

        return ProductCategory::query()
            ->get()
            ->sortBy(fn (ProductCategory $category) => $category->getTranslation('name', $locale))
            ->values();
    }

    public function store(array $data): ?ProductCategory
    {
        return $this->execute(function () use ($data) {
            $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
            $data = $this->prepareCategoryData($data);

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

            $data = $this->prepareCategoryUpdateData($data, $category);
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

    private function prepareCategoryData(array $data): array
    {
        $autoTranslate = wantsAutoTranslate($data);
        unset($data['auto_translate']);

        return array_merge($data, buildTranslations([
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
        ], $autoTranslate));
    }

    private function prepareCategoryUpdateData(array $data, ProductCategory $category): array
    {
        $locale = app()->getLocale();
        $autoTranslate = wantsAutoTranslate($data);
        unset($data['auto_translate']);

        return array_merge($data, buildTranslations([
            'name' => $data['name'] ?? $category->getTranslation('name', $locale, false),
            'description' => $data['description'] ?? $category->getTranslation('description', $locale, false),
        ], $autoTranslate, [
            'name' => $category->getTranslations('name'),
            'description' => $category->getTranslations('description'),
        ]));
    }
}

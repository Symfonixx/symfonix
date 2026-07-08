<?php

namespace Modules\Cms\Repositories\BlogCategory;

use Config;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Cms\Models\BlogCategory;
use Modules\Core\Traits\ExceptionHandlerTrait;

class BlogCategoryModelRepository implements BlogCategoryRepository
{
    use ExceptionHandlerTrait;

    public function all(array $columns = ['*']): LengthAwarePaginator
    {
        return BlogCategory::select($columns)
            ->withCount('blogs')
            ->latest()
            ->paginate(Config::get('core.page_size', 10));
    }

    public function find(int $id, array $columns = ['*']): ?BlogCategory
    {
        return BlogCategory::find($id, $columns);
    }

    public function store(array $data): mixed
    {
        return $this->execute(function () use ($data) {
            $categoryData = $this->prepareCategoryData($data);
            BlogCategory::create($categoryData);
            session()->flushMessage(true);
        });
    }

    private function prepareCategoryData(array $data): array
    {
        if (is_array($data['name'] ?? null)) {
            $name = $data['name'][app()->getLocale()]
                ?? $data['name'][array_key_first($data['name'])]
                ?? '';

            return array_merge($data, buildTranslations(
                ['name' => (string) $name],
                wantsAutoTranslate($data),
                ['name' => array_map('strval', $data['name'])]
            ));
        }

        return array_merge($data, buildTranslations([
            'name' => $data['name'] ?? '',
        ], wantsAutoTranslate($data)));
    }

    private function prepareCategoryUpdateData(array $data, BlogCategory $category): array
    {
        if (is_array($data['name'] ?? null)) {
            $existing = $category->getTranslations('name');
            $provided = array_map('strval', $data['name']);
            $merged = array_merge($existing, $provided);

            if (wantsAutoTranslate($data)) {
                $source = $provided[app()->getLocale()]
                    ?? $merged[app()->getLocale()]
                    ?? reset($merged)
                    ?: '';

                return array_merge($data, buildTranslations(
                    ['name' => (string) $source],
                    true,
                    ['name' => $merged]
                ));
            }

            return array_merge($data, ['name' => $merged]);
        }

        return array_merge($data, buildTranslations([
            'name' => $data['name'] ?? '',
        ], wantsAutoTranslate($data), [
            'name' => $category->getTranslations('name'),
        ]));
    }

    public function update(array $data, BlogCategory $category): mixed
    {
        return $this->execute(function () use ($data, $category) {
            $categoryData = $this->prepareCategoryUpdateData($data, $category);
            $category->update($categoryData);
            session()->flushMessage(true);

            return true;
        });
    }

    public function deleteMulti(array $ids): ?bool
    {
        return $this->execute(function () use ($ids) {
            // If BlogCategory ever has images/files, delete them here (structure for extensibility)
            BlogCategory::destroy($ids);
            // Optionally clear cache or handle related cleanup here
            session()->flushMessage(true);

            return true;
        });
    }
}

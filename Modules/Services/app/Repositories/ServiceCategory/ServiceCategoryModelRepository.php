<?php

namespace Modules\Services\Repositories\ServiceCategory;

use Config;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Log;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Core\Traits\FileTrait;
use Modules\Services\Models\ServiceCategory;

class ServiceCategoryModelRepository implements ServiceCategoryRepository
{
    use ExceptionHandlerTrait, FileTrait;

    private string $uploadPath = 'service_categories';

    public function all(array $columns = ['*']): LengthAwarePaginator
    {
        return ServiceCategory::select($columns)
            ->withCount('services')
            ->latest()
            ->paginate(Config::get('core.page_size', 10));
    }

    public function find(int $id, array $columns = ['*']): ?ServiceCategory
    {
        return ServiceCategory::find($id, $columns);
    }

    public function store(array $data): mixed
    {
        return $this->execute(function () use ($data) {
            $categoryData = $this->prepareCategoryData($data);
            ServiceCategory::create($categoryData);
            session()->flushMessage(true);
        });
    }

    private function prepareCategoryData(array $data, ?string $existingImage = null): array
    {
        $path = $data['image'] ? $this->upload($data['image'], $this->uploadPath, $data['slug'], $existingImage) : $existingImage;
        $transTitle = [app()->getLocale() => $data['title']];
        $transDesc = [app()->getLocale() => $data['description'] ?? ''];
        foreach (otherLangs() as $lang) {
            try {
                $transTitle[$lang] = autoGoogleTranslator($lang, $data['title'] ?? '');
                $transDesc[$lang] = autoGoogleTranslator($lang, $data['description'] ?? '');
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }

        return array_merge($data, [
            'image' => $path,
            'title' => $transTitle,
            'description' => $transDesc,
        ]);
    }

    /**
     * Prepare service category data for update without auto translation.
     * Only the current locale is updated; other locales remain untouched.
     */
    private function prepareCategoryUpdateData(array $data, ServiceCategory $category): array
    {
        $path = $data['image'] ? $this->upload($data['image'], $this->uploadPath, $data['slug'], $category->image) : $category->image;

        $locale = app()->getLocale();

        $transTitle = $category->getTranslations('title');
        $transDesc = $category->getTranslations('description');

        $transTitle[$locale] = $data['title'] ?? ($transTitle[$locale] ?? '');
        $transDesc[$locale] = $data['description'] ?? ($transDesc[$locale] ?? '');

        return array_merge($data, [
            'image' => $path,
            'title' => $transTitle,
            'description' => $transDesc,
        ]);
    }

    public function update(array $data, ServiceCategory $category): mixed
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
            $images = ServiceCategory::whereIn('id', $ids)->pluck('image')->filter()->toArray();
            ServiceCategory::destroy($ids);
            $this->deleteFile($images);
            session()->flushMessage(true);

            return true;
        });
    }
}





<?php

namespace Modules\Product\Repositories;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Core\Traits\FileTrait;
use Modules\Product\Models\Product;

class ProductRepository
{
    use ExceptionHandlerTrait, FileTrait;

    private string $uploadPath = 'products';

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Product::query()
            ->with('category')
            ->filter($filters)
            ->latest()
            ->paginate($perPage);
    }

    public function publishedPaginate(int $perPage = 12): LengthAwarePaginator
    {
        return Product::query()
            ->published()
            ->active()
            ->with('category:id,name,slug')
            ->latest()
            ->paginate($perPage);
    }

    public function activeProducts(): Collection
    {
        return Product::query()
            ->active()
            ->with('category')
            ->orderBy('name')
            ->get();
    }

    public function store(array $data): ?Product
    {
        return $this->execute(function () use ($data) {
            $data['sku'] = $data['sku'] ?? strtoupper(Str::random(8));
            $data = $this->handleUploads($data);
            $data = $this->prepareProductData($data);

            $product = Product::query()->create($data);
            session()->flushMessage(true);

            return $product;
        });
    }

    public function update(Product $product, array $data): ?Product
    {
        return $this->execute(function () use ($product, $data) {
            $data = $this->handleUploads($data, $product);
            $data = $this->prepareProductUpdateData($data, $product);

            $product->update($data);
            session()->flushMessage(true);

            return $product;
        });
    }

    public function togglePublished(Product $product): ?Product
    {
        return $this->execute(function () use ($product) {
            $product->update(['is_published' => ! $product->is_published]);
            session()->flushMessage(true);

            return $product->fresh();
        });
    }

    public function delete(Product $product): ?bool
    {
        return $this->execute(function () use ($product) {
            if ($product->sales()->exists()) {
                $product->update(['status' => Product::STATUS_ARCHIVED]);
                session()->flushMessage(true, __('product::product.messages.archived_instead'));

                return true;
            }

            if ($product->main_image) {
                $this->deleteFile($product->main_image);
            }

            if ($product->seo_meta_img) {
                $this->deleteFile($product->seo_meta_img);
            }

            $deleted = $product->delete();
            session()->flushMessage(true);

            return $deleted;
        });
    }

    private function prepareProductData(array $data): array
    {
        $autoTranslate = wantsAutoTranslate($data);
        unset($data['auto_translate']);

        return array_merge($data, buildTranslations([
            'name' => $data['name'] ?? '',
            'short_description' => $data['short_description'] ?? '',
            'description' => $data['description'] ?? '',
            'seo_title' => $data['seo_title'] ?? '',
            'seo_description' => $data['seo_description'] ?? '',
            'seo_keywords' => $data['seo_keywords'] ?? '',
        ], $autoTranslate));
    }

    private function prepareProductUpdateData(array $data, Product $product): array
    {
        $locale = app()->getLocale();
        $autoTranslate = wantsAutoTranslate($data);
        unset($data['auto_translate']);

        return array_merge($data, buildTranslations([
            'name' => $data['name'] ?? $product->getTranslation('name', $locale, false),
            'short_description' => $data['short_description'] ?? $product->getTranslation('short_description', $locale, false),
            'description' => $data['description'] ?? $product->getTranslation('description', $locale, false),
            'seo_title' => $data['seo_title'] ?? $product->getTranslation('seo_title', $locale, false),
            'seo_description' => $data['seo_description'] ?? $product->getTranslation('seo_description', $locale, false),
            'seo_keywords' => $data['seo_keywords'] ?? $product->getTranslation('seo_keywords', $locale, false),
        ], $autoTranslate, [
            'name' => $product->getTranslations('name'),
            'short_description' => $product->getTranslations('short_description'),
            'description' => $product->getTranslations('description'),
            'seo_title' => $product->getTranslations('seo_title'),
            'seo_description' => $product->getTranslations('seo_description'),
            'seo_keywords' => $product->getTranslations('seo_keywords'),
        ]));
    }

    private function handleUploads(array $data, ?Product $product = null): array
    {
        if (array_key_exists('main_image', $data)) {
            $data['main_image'] = $this->resolveImageUpload($data['main_image'] ?? null, $product?->main_image);
        }

        if (array_key_exists('seo_meta_img', $data)) {
            $data['seo_meta_img'] = $this->resolveImageUpload($data['seo_meta_img'] ?? null, $product?->seo_meta_img);
        }

        return $data;
    }

    private function resolveImageUpload(mixed $file, ?string $existing = null): ?string
    {
        if ($file instanceof UploadedFile) {
            return $this->upload($file, $this->uploadPath, null, $existing, 1200);
        }

        return $existing;
    }
}

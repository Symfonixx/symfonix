<?php

namespace Modules\Product\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Product\Models\Product;

class ProductRepository
{
    use ExceptionHandlerTrait;

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Product::query()
            ->with('category')
            ->filter($filters)
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
            $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
            $data['sku'] = $data['sku'] ?? strtoupper(Str::random(8));

            $product = Product::query()->create($data);
            session()->flushMessage(true);

            return $product;
        });
    }

    public function update(Product $product, array $data): ?Product
    {
        return $this->execute(function () use ($product, $data) {
            if (empty($data['slug']) && ! empty($data['name'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            $product->update($data);
            session()->flushMessage(true);

            return $product;
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

            $deleted = $product->delete();
            session()->flushMessage(true);

            return $deleted;
        });
    }
}

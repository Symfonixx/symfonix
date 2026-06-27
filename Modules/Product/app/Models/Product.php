<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_ARCHIVED = 'archived';

    public const BILLING_ONE_TIME = 'one_time';

    public const BILLING_MONTHLY = 'monthly';

    public const BILLING_QUARTERLY = 'quarterly';

    public const BILLING_YEARLY = 'yearly';

    protected $fillable = [
        'product_category_id',
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'currency',
        'billing_type',
        'status',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        if (! empty($filters['product_category_id'])) {
            $query->where('product_category_id', (int) $filters['product_category_id']);
        }

        if (array_key_exists('status', $filters) && $filters['status'] !== '' && $filters['status'] !== null) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(ProductSale::class);
    }
}

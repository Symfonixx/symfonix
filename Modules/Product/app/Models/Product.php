<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Modules\Product\Enums\ProductBillingType;
use Modules\Product\Enums\ProductStatus;
use Modules\Tax\Models\TaxRate;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasTranslations;

    public const STATUS_ACTIVE = ProductStatus::ACTIVE->value;

    public const STATUS_ARCHIVED = ProductStatus::ARCHIVED->value;

    public const BILLING_ONE_TIME = ProductBillingType::ONE_TIME->value;

    public const BILLING_MONTHLY = ProductBillingType::MONTHLY->value;

    public const BILLING_QUARTERLY = ProductBillingType::QUARTERLY->value;

    public const BILLING_YEARLY = ProductBillingType::YEARLY->value;

    public array $translatable = [
        'name',
        'short_description',
        'description',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    protected $fillable = [
        'product_category_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'main_image',
        'seo_meta_img',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'price',
        'currency',
        'tax_rate_id',
        'billing_type',
        'status',
        'is_featured',
        'is_published',
    ];

    protected $appends = [
        'main_image_link',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $name = static::resolveSlugSource($product);

                if ($name) {
                    $product->slug = static::generateUniqueSlug($name);
                }
            }
        });

        static::updating(function (Product $product) {
            if ($product->isDirty('name') && ! $product->isDirty('slug')) {
                $name = static::resolveSlugSource($product);

                if ($name) {
                    $product->slug = static::generateUniqueSlug($name, $product->id);
                }
            }
        });
    }

    protected static function resolveSlugSource(Product $product): string
    {
        return $product->getTranslation('name', 'en', false)
            ?: $product->getTranslation('name', app()->getLocale(), false)
            ?: '';
    }

    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (static::query()
            ->when($ignoreId, fn (Builder $q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $original.'-'.$count++;
        }

        return $slug;
    }

    public function seoTitle(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $title = $this->getTranslation('seo_title', $locale, false);

        if ($title) {
            return $title;
        }

        return $this->getTranslation('name', $locale) ?: '';
    }

    public function seoDescription(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $description = $this->getTranslation('seo_description', $locale, false);

        if ($description) {
            return $description;
        }

        $shortDescription = $this->getTranslation('short_description', $locale, false);

        if ($shortDescription) {
            return $shortDescription;
        }

        return Str::limit(strip_tags((string) $this->getTranslation('description', $locale, false)), 160, '');
    }

    public function seoKeywords(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();

        return $this->getTranslation('seo_keywords', $locale, false) ?: null;
    }

    public function seoMetaImageLink(): string
    {
        if (! empty($this->seo_meta_img)) {
            return asset('storage/'.$this->seo_meta_img);
        }

        return $this->main_image_link;
    }

    public function getMainImageLinkAttribute(): string
    {
        if (! empty($this->attributes['main_image'])) {
            return asset('storage/'.$this->attributes['main_image']);
        }

        return asset('images/blank.png');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        if (! empty($filters['product_category_id'])) {
            $query->where('product_category_id', (int) $filters['product_category_id']);
        }

        if (array_key_exists('status', $filters) && $filters['status'] !== '' && $filters['status'] !== null) {
            $query->where('status', $filters['status']);
        }

        if (array_key_exists('is_published', $filters) && $filters['is_published'] !== '' && $filters['is_published'] !== null) {
            $query->where('is_published', filter_var($filters['is_published'], FILTER_VALIDATE_BOOLEAN));
        }

        if (! empty($filters['search'])) {
            $search = mb_strtolower($filters['search'], 'UTF-8');
            $locales = array_keys(config('laravellocalization.supportedLocales', ['en' => [], 'ar' => []]));

            $query->where(function (Builder $q) use ($search, $locales) {
                $q->where('sku', 'like', "%{$search}%");

                foreach ($locales as $locale) {
                    $q->orWhereRaw(
                        "LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}'))) LIKE ?",
                        ["%{$search}%"]
                    )
                        ->orWhereRaw(
                            "LOWER(JSON_UNQUOTE(JSON_EXTRACT(short_description, '$.{$locale}'))) LIKE ?",
                            ["%{$search}%"]
                        )
                        ->orWhereRaw(
                            "LOWER(JSON_UNQUOTE(JSON_EXTRACT(description, '$.{$locale}'))) LIKE ?",
                            ["%{$search}%"]
                        );
                }
            });
        }

        return $query;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(ProductSale::class);
    }
}

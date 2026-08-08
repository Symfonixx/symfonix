<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Base\Models\Seo;
use Modules\Base\Support\Meta;
use Modules\Base\Support\Schema;
use Modules\Product\Models\Product;
use Modules\Product\Repositories\ProductRepository;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepository $productRepository,
    ) {}

    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $products = $this->productRepository->publishedPaginate(12);

        $siteName = Seo::get('website_name', config('app.name'));
        $canonical = route('product.index');
        $meta = (new Meta)
            ->title(__('product::product.pages.catalog_title').' | '.$siteName)
            ->description(__('product::product.meta.index_description'))
            ->keywords(__('product::product.meta.index_keywords'))
            ->ogImage()
            ->twitterImage()
            ->canonical($canonical)
            ->toArray();

        $listItems = $products->getCollection()->take(20)->map(function (Product $product) use ($locale) {
            return [
                'name' => $product->getTranslation('name', $locale) ?: $product->name,
                'url' => route('product.show', ['slug' => $product->slug]),
            ];
        })->values()->all();

        return $this->inertia('Product::ProductIndex', [
            'products' => $products->through(fn (Product $product) => $this->mapProduct($product, $locale)),
            'structuredData' => [
                Schema::breadcrumbs([
                    ['name' => __('Home'), 'url' => route('home')],
                    ['name' => __('product::product.pages.catalog_title'), 'url' => $canonical],
                ]),
                Schema::itemList(__('product::product.pages.catalog_title'), $listItems, $canonical),
            ],
        ], $meta);
    }

    public function show(string $slug)
    {
        $locale = app()->getLocale();
        $product = Product::query()
            ->published()
            ->active()
            ->where('slug', $slug)
            ->with('category:id,name,slug')
            ->firstOrFail();

        $related = Product::query()
            ->published()
            ->active()
            ->where('id', '!=', $product->id)
            ->when($product->product_category_id, fn ($query) => $query->where('product_category_id', $product->product_category_id))
            ->with('category:id,name,slug')
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn (Product $item) => $this->mapProduct($item, $locale));

        $canonical = route('product.show', ['slug' => $product->slug]);

        $meta = (new Meta)
            ->title($product->seoTitle($locale).' | '.Seo::get('website_name', config('app.name')))
            ->description($product->seoDescription($locale))
            ->keywords($product->seoKeywords($locale))
            ->ogImage($product->seoMetaImageLink())
            ->twitterImage($product->seoMetaImageLink())
            ->type('product')
            ->canonical($canonical)
            ->toArray();

        $structuredData = [
            Schema::breadcrumbs([
                ['name' => __('Home'), 'url' => route('home')],
                ['name' => __('product::product.pages.catalog_title'), 'url' => route('product.index')],
                ['name' => $product->getTranslation('name', $locale), 'url' => $canonical],
            ]),
            Schema::product([
                'name' => $product->getTranslation('name', $locale),
                'description' => $product->seoDescription($locale) ?: $product->getTranslation('short_description', $locale),
                'image' => $product->main_image_link,
                'url' => $canonical,
                'sku' => $product->sku,
                'category' => $product->category?->getTranslation('name', $locale),
                'price' => $product->price,
                'currency' => $product->currency ?: 'USD',
                'availability' => $product->status === Product::STATUS_ACTIVE
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'locale' => $locale,
            ]),
        ];

        return $this->inertia('Product::ProductShow', [
            'structuredData' => $structuredData,
            'product' => $this->mapProduct($product, $locale, detailed: true),
            'relatedProducts' => $related,
        ], $meta);
    }

    private function mapProduct(Product $product, string $locale, bool $detailed = false): array
    {
        $data = [
            'id' => $product->id,
            'name' => $product->getTranslation('name', $locale),
            'slug' => $product->slug,
            'short_description' => $product->getTranslation('short_description', $locale),
            'main_image_link' => $product->main_image_link,
            'is_featured' => $product->is_featured,
            'price' => $product->price,
            'currency' => $product->currency ?: 'USD',
            'billing_type' => $product->billing_type,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->getTranslation('name', $locale),
                'slug' => $product->category->slug,
            ] : null,
        ];

        if ($detailed) {
            $data['description'] = $product->getTranslation('description', $locale);
            $data['sku'] = $product->sku;
            $data['seo_title'] = $product->seoTitle($locale);
            $data['seo_description'] = $product->seoDescription($locale);
        }

        return $data;
    }
}

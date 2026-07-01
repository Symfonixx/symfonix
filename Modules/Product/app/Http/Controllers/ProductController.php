<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Base\Models\Seo;
use Modules\Base\Support\Meta;
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
        $meta = (new Meta)
            ->title(__('product::product.pages.catalog_title').' | '.$siteName)
            ->description(__('product::product.meta.index_description'))
            ->keywords(__('product::product.meta.index_keywords'))
            ->ogImage()
            ->twitterImage()
            ->toArray();

        return $this->inertia('Product::ProductIndex', [
            'products' => $products->through(fn (Product $product) => $this->mapProduct($product, $locale)),
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

        $meta = (new Meta)
            ->title($product->seoTitle($locale).' | '.Seo::get('website_name', config('app.name')))
            ->description($product->seoDescription($locale))
            ->keywords($product->seoKeywords($locale))
            ->ogImage($product->seoMetaImageLink())
            ->twitterImage($product->seoMetaImageLink())
            ->toArray();

        return $this->inertia('Product::ProductShow', [
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
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
        ];

        if ($detailed) {
            $data['description'] = $product->getTranslation('description', $locale);
            $data['seo_title'] = $product->seoTitle($locale);
            $data['seo_description'] = $product->seoDescription($locale);
        }

        return $data;
    }
}

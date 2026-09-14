<?php

namespace Modules\Base\Http\Controllers;

use App\Http\Controllers\Controller;
use Cache;
use Modules\Base\Models\Seo;
use Modules\Base\Support\Meta;
use Modules\Base\Support\Schema;
use Modules\Cms\Models\Blog;
use Modules\Cms\Models\Client;
use Modules\Product\Models\Product;
use Modules\Project\Models\ProjectUseCase;
use Modules\Services\Models\ServiceCategory;
use Modules\Team\Models\Team;
use Modules\Testimonial\Models\Testimonial;
use Vdhicts\ReadTime\ReadTime;

class HomeController extends Controller
{
    public function index()
    {
        // Note: cache key versioned to avoid stale cached structure after we map fields for the new UI.
        $locale = app()->getLocale();
        $posts = Blog::featured()
            ->with('category')
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($blog) use ($locale) {
                return [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'image_link' => $blog->image_link,
                    'description' => $blog->description,
                    'keywords' => $blog->keywords,
                    'reading_time' => $this->getReadingTimeMinutes($blog, $locale),
                    'created_at' => $blog->created_at ? $blog->created_at->format('d M Y') : null,
                    'category' => $blog->category ? [
                        'id' => $blog->category->id,
                        'name' => $blog->category->name,
                        'slug' => $blog->category->slug,
                    ] : null,
                ];
            });

        $servicesCategories = ServiceCategory::with(['services' => function ($q) {
            $q->published()->take(5);
        }])->latest()->get();

        $testimonials = Testimonial::published()
            ->withDisplayRelations()
            ->latest()
            ->take(10)
            ->get();

        $teams = Team::query()->published()
            ->latest()
            ->take(10)
            ->inRandomOrder()
            ->get();

        $featuredUseCases = ProjectUseCase::query()
            ->published()
            ->featured()
            ->ordered()
            ->limit(6)
            ->get();

        if ($featuredUseCases->isEmpty()) {
            $featuredUseCases = ProjectUseCase::query()
                ->published()
                ->ordered()
                ->limit(6)
                ->get();
        }

        $useCases = $featuredUseCases->map(function (ProjectUseCase $useCase) use ($locale) {
            return [
                'id' => $useCase->id,
                'slug' => $useCase->slug,
                'title' => $useCase->getTranslation('title', $locale),
                'summary' => $useCase->getTranslation('summary', $locale),
                'image_link' => $useCase->image_link,
                'technologies' => $useCase->technologies ?? [],
                'category_tag' => $useCase->category_tag,
                'completed_year' => $useCase->completed_year,
            ];
        });

        $featuredProducts = Product::query()
            ->published()
            ->active()
            ->where('is_featured', true)
            ->with('category:id,name,slug')
            ->latest()
            ->limit(6)
            ->get();

        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::query()
                ->published()
                ->active()
                ->with('category:id,name,slug')
                ->latest()
                ->limit(6)
                ->get();
        }

        $products = $featuredProducts->map(function (Product $product) use ($locale) {
            return [
                'id' => $product->id,
                'name' => $product->getTranslation('name', $locale),
                'slug' => $product->slug,
                'short_description' => $product->getTranslation('short_description', $locale),
                'main_image_link' => $product->main_image_link,
                'is_featured' => $product->is_featured,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->getTranslation('name', $locale),
                    'slug' => $product->category->slug,
                ] : null,
            ];
        });

        $clients = Client::published()
            ->ordered()
            ->get()
            ->map(function (Client $client) use ($locale) {
                return [
                    'id' => $client->id,
                    'name' => $client->getTranslation('name', $locale),
                    'logo_link' => $client->logo_link,
                    'url' => $client->url,
                ];
            });

        $siteName = Seo::get('website_name', config('app.name'));
        $canonical = route('home');
        $meta = (new Meta)
            ->title(__('Home').' | '.$siteName)
            ->description(__('Empowering businesses with modern web, mobile, AI, and cloud solutions.'))
            ->keywords(__('IT solutions, web development, mobile apps, AI automation, cloud services'))
            ->ogImage()
            ->twitterImage()
            ->canonical($canonical)
            ->toArray();

        return $this->inertia('Base::Index', [
            'posts' => $posts,
            'servicesCategories' => $servicesCategories,
            'testimonials' => $testimonials,
            'teams' => $teams,
            'useCases' => $useCases,
            'products' => $products,
            'clients' => $clients,
            'structuredData' => [
                Schema::webPage([
                    'type' => 'WebPage',
                    'title' => __('Home').' | '.$siteName,
                    'description' => __('Empowering businesses with modern web, mobile, AI, and cloud solutions.'),
                    'url' => $canonical,
                ]),
            ],
        ], $meta);
    }

    private function getReadingTimeMinutes(Blog $blog, string $locale): int
    {
        $content = $blog->getTranslation('content', $locale)
            ?: $blog->getTranslation('description', $locale)
            ?: '';

        if (trim(strip_tags((string) $content)) === '') {
            return 0;
        }

        return (new ReadTime($content))->minutes();
    }
}

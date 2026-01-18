<?php

namespace Modules\Services\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Modules\Base\Models\Seo;
use Modules\Base\Models\Settings;
use Modules\Services\Models\Service;
use Modules\Services\Models\ServiceCategory;
use Modules\SearchEngine\Models\SearchKeyword;
use Modules\Testimonial\Models\Testimonial;

class ServiceController extends Controller {
    public function index(Request $request) {
        $locale = app()->getLocale();
        $query = Service::published()->with('category')->latest();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = mb_strtolower(trim($request->search), 'UTF-8');
            $locales = array_keys(config('laravellocalization.supportedLocales', ['en' => [], 'ar' => []]));
            $query->where(function ($q) use ($search, $locales) {
                $firstLocale = true;
                foreach ($locales as $loc) {
                    if ($firstLocale) {
                        $q->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$loc}'))) LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(description, '$.{$loc}'))) LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(content, '$.{$loc}'))) LIKE ?", ["%{$search}%"]);
                        $firstLocale = false;
                    } else {
                        $q->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$loc}'))) LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(description, '$.{$loc}'))) LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(content, '$.{$loc}'))) LIKE ?", ["%{$search}%"]);
                    }
                }
            });

            // Track search keyword
            if (!empty($search)) {
                $keyword = SearchKeyword::firstOrNew(['keyword' => $search]);
                $keyword->count = ($keyword->count ?? 0) + 1;
                $keyword->save();
            }
        }

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $services = $query->paginate(12)->through(function ($service) {
            return [
                'id' => $service->id,
                'title' => $service->title,
                'slug' => $service->slug,
                'image_link' => $service->image_link,
                'description' => $service->description,
                'keywords' => $service->keywords,
                'created_at' => $service->created_at->format('d M Y'),
                'category' => $service->category ? [
                    'id' => $service->category->id,
                    'title' => $service->category->title,
                    'slug' => $service->category->slug,
                    'image_link' => $service->category->image_link,
                ] : null,
            ];
        });

        $categories = ServiceCategory::withCount(['services' => function ($q) {
            $q->where('status', 'Published');
        }])->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'services_count' => $category->services_count,
                'image_link' => $category->image_link,
            ];
        });

        // Recent services for sidebar
        $recentServices = Service::published()
            ->latest()
            ->limit(6)
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'title' => $service->title,
                    'slug' => $service->slug,
                    'image_link' => $service->image_link,
                    'created_at' => $service->created_at->format('d M Y'),
                ];
            });

        return Inertia::render('Services::ServiceIndex', [
            'services' => $services,
            'categories' => $categories,
            'recentServices' => $recentServices,
            'filters' => [
                'search' => $request->search,
                'category' => $request->category,
            ],
        ]);
    }

    public function show($slug) {
        $service = Service::published()
            ->where('slug', $slug)
            ->with('category')
            ->firstOrFail();

        // Increment visits
        if (!session()->has('service_'.$service->id)) {
            $service->increment('visits');
            session()->put('service_'.$service->id, true);
        }

        // Related services (same category, excluding current)
        $relatedServices = Service::published()
            ->where('service_category_id', $service->service_category_id)
            ->where('id', '!=', $service->id)
            ->latest()
            ->limit(3)
            ->with('category')
            ->get();

        // If not enough related services, get recent ones
        if ($relatedServices->count() < 3) {
            $additionalServices = Service::published()
                ->where('id', '!=', $service->id)
                ->whereNotIn('id', $relatedServices->pluck('id'))
                ->latest()
                ->limit(3 - $relatedServices->count())
                ->get();
            $relatedServices = $relatedServices->merge($additionalServices);
        }

        // Categories for sidebar
        $categories = ServiceCategory::withCount(['services' => function ($q) {
            $q->where('status', 'Published');
        }])->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'services_count' => $category->services_count,
                'image_link' => $category->image_link,
            ];
        });

        // Recent services for sidebar
        $recentServices = Service::published()
            ->where('id', '!=', $service->id)
            ->latest()
            ->limit(6)
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'title' => $service->title,
                    'slug' => $service->slug,
                    'image_link' => $service->image_link,
                    'created_at' => $service->created_at->format('d M Y'),
                ];
            });

        // Previous and next services
        $previousService = Service::published()
            ->where('id', '<', $service->id)
            ->latest('id')
            ->first();

        $nextService = Service::published()
            ->where('id', '>', $service->id)
            ->oldest('id')
            ->first();

        // Testimonials for service show page
        $testimonials = Testimonial::where('status', 'Published')
            ->latest()
            ->take(10)
            ->get();

        // Build dynamic meta data for this service
        $seo = Seo::pluck('value', 'key');
        $settings = Settings::pluck('value', 'key');

        $baseTitle = $seo->get('website_name');
        $serviceTitle = (string) $service->title;

        $rawDescription = $service->description ?? $service->content ?? $seo->get('website_desc');
        $plainDescription = Str::limit(trim(strip_tags((string) $rawDescription)), 160, '...');

        $metaImage = $service->image_link ?: $settings->get('meta_img');

        $meta = [
            'title' => $serviceTitle.($baseTitle ? " | {$baseTitle}" : ''),
            'description' => $plainDescription,
            'robots' => 'index, follow',
            'canonical' => url()->current(),
            'og' => [
                'title' => $serviceTitle.($baseTitle ? " | {$baseTitle}" : ''),
                'description' => $plainDescription,
                'image' => $metaImage,
            ],
            'twitter' => [
                'title' => $serviceTitle.($baseTitle ? " | {$baseTitle}" : ''),
                'description' => $plainDescription,
                'image' => $metaImage,
            ],
        ];

        return Inertia::render('Services::ServiceShow', [
            'service' => [
                'id' => $service->id,
                'title' => $service->title,
                'slug' => $service->slug,
                'image_link' => $service->image_link,
                'description' => $service->description,
                'content' => $service->content,
                'keywords' => $service->keywords,
                'created_at' => $service->created_at->format('d M Y'),
                'created_at_formatted' => $service->created_at->format('d M Y'),
                'category' => $service->category ? [
                    'id' => $service->category->id,
                    'title' => $service->category->title,
                    'slug' => $service->category->slug,
                ] : null,
            ],
            'relatedServices' => $relatedServices->map(function ($service) {
                return [
                    'id' => $service->id,
                    'title' => $service->title,
                    'slug' => $service->slug,
                    'image_link' => $service->image_link,
                    'description' => $service->description,
                    'keywords' => $service->keywords,
                    'created_at' => $service->created_at->format('d M Y'),
                    'category' => $service->category ? [
                        'id' => $service->category->id,
                        'title' => $service->category->title,
                        'slug' => $service->category->slug,
                        'image_link' => $service->category->image_link,
                    ] : null,
                ];
            }),
            'categories' => $categories,
            'recentServices' => $recentServices,
            'previousService' => $previousService ? [
                'id' => $previousService->id,
                'title' => $previousService->title,
                'slug' => $previousService->slug,
            ] : null,
            'nextService' => $nextService ? [
                'id' => $nextService->id,
                'title' => $nextService->title,
                'slug' => $nextService->slug,
            ] : null,
            'testimonials' => $testimonials,
            'meta' => $meta,
        ]);
    }
}


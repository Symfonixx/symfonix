<?php

namespace Modules\Services\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Base\Models\Seo;
use Modules\Base\Support\Meta;
use Modules\SearchEngine\Models\SearchKeyword;
use Modules\Services\Models\Service;
use Modules\Services\Models\ServiceCategory;
use Modules\Testimonial\Models\Testimonial;
use Vdhicts\ReadTime\ReadTime;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $query = Service::published()->with('category');

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
            if (! empty($search)) {
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

        $totalServicesCount = Service::published()->count();
        $services = $query->paginate(12)->through(function ($service) use ($locale) {
            return [
                'id' => $service->id,
                'title' => $service->title,
                'slug' => $service->slug,
                'image_link' => $service->image_link,
                'description' => $service->description,
                'keywords' => $service->keywords,
                'reading_time' => $this->getReadingTimeMinutes($service, $locale),
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
        $siteName = Seo::get('website_name', config('app.name'));
        $meta = (new Meta)
            ->title(__('Our Services').' | '.$siteName)
            ->description(__('Discover our IT services designed to scale and modernize your business.'))
            ->keywords(__('IT services, web development, mobile apps, AI solutions, cloud services'))
            ->ogImage()
            ->twitterImage()
            ->toArray();

        return $this->inertia('Services::ServiceIndex', [
            'services' => $services,
            'categories' => $categories,
            'recentServices' => $recentServices,
            'totalServicesCount' => $totalServicesCount,
            'filters' => [
                'search' => $request->search,
                'category' => $request->category,
            ],
        ], $meta);
    }

    public function show($slug)
    {
        $locale = app()->getLocale();
        $service = Service::published()
            ->where('slug', $slug)
            ->with('category')
            ->firstOrFail();

        // Increment visits
        if (! session()->has('service_'.$service->id)) {
            $service->increment('visits');
            session()->put('service_'.$service->id, true);
        }

        $totalServicesCount = Service::published()->count();
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

        $meta = (new Meta)
            ->title($service->title)
            ->description($service->description)
            ->keywords($service->keywords)
            ->ogImage($service->image_link)
            ->twitterImage($service->image_link)
            ->toArray();

        return $this->inertia('Services::ServiceShow', [
            'service' => [
                'id' => $service->id,
                'title' => $service->title,
                'slug' => $service->slug,
                'image_link' => $service->image_link,
                'description' => $service->description,
                'content' => $service->content,
                'keywords' => $service->keywords,
                'reading_time' => $this->getReadingTimeMinutes($service, $locale),
                'created_at' => $service->created_at->format('d M Y'),
                'created_at_formatted' => $service->created_at->format('d M Y'),
                'category' => $service->category ? [
                    'id' => $service->category->id,
                    'title' => $service->category->title,
                    'slug' => $service->category->slug,
                ] : null,
            ],
            'relatedServices' => $relatedServices->map(function ($service) use ($locale) {
                return [
                    'id' => $service->id,
                    'title' => $service->title,
                    'slug' => $service->slug,
                    'image_link' => $service->image_link,
                    'description' => $service->description,
                    'keywords' => $service->keywords,
                    'reading_time' => $this->getReadingTimeMinutes($service, $locale),
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
            'totalServicesCount' => $totalServicesCount,
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
        ], $meta);
    }

    private function getReadingTimeMinutes(Service $service, string $locale): int
    {
        $content = $service->getTranslation('content', $locale)
            ?: $service->getTranslation('description', $locale)
                ?: '';

        if (trim(strip_tags((string) $content)) === '') {
            return 0;
        }

        return (new ReadTime($content))->minutes();
    }
}

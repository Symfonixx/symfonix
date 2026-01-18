<?php

namespace Modules\Base\Http\Controllers;

use App\Http\Controllers\Controller;
use Cache;
use Inertia\Inertia;
use Modules\Cms\Models\Blog;
use Modules\Services\Models\Service;
use Modules\Services\Models\ServiceCategory;
use Modules\Team\Models\Team;
use Modules\Testimonial\Models\Testimonial;

class HomeController extends Controller {

    public function index() {
        // Note: cache key versioned to avoid stale cached structure after we map fields for the new UI.
        $posts =  Blog::featured()
                ->with('category')
                ->latest()
                ->take(6)
                ->get()
                ->map(function ($blog) {
                    return [
                        'id' => $blog->id,
                        'title' => $blog->title,
                        'slug' => $blog->slug,
                        'image_link' => $blog->image_link,
                        'description' => $blog->description,
                        'keywords' => $blog->keywords,
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

        $testimonials = Testimonial::where('status', 'Published')
            ->latest()
            ->take(10)
            ->get();

        $teams = Team::where('status', 'Published')
            ->latest()
            ->take(10)
            ->get();

        return Inertia::render('Base::Index', [
            'posts' => $posts,
            'servicesCategories' => $servicesCategories,
            'testimonials' => $testimonials,
            'teams' => $teams,
        ]);
    }
}

<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Base\Support\Meta;
use Modules\Cms\Models\Blog;
use Modules\Cms\Models\BlogCategory;
use Modules\SearchEngine\Models\SearchKeyword;

class BlogController extends Controller {
    public function index(Request $request) {
        $locale = app()->getLocale();
        $query = Blog::published()->with('category')->latest();

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

        $blogs = $query->paginate(12)->through(function ($blog) {
            return [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'image_link' => $blog->image_link,
                'description' => $blog->description,
                'created_at' => $blog->created_at->format('d M Y'),
                'created_at_day' => $blog->created_at->format('d'),
                'created_at_month' => $blog->created_at->format('M'),
                'category' => $blog->category ? [
                    'id' => $blog->category->id,
                    'name' => $blog->category->name,
                    'slug' => $blog->category->slug,
                ] : null,
            ];
        });

        $categories = BlogCategory::withCount(['blogs' => function ($q) {
            $q->where('status', 'Published');
        }])->get()->map(function ($category) use ($locale) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'blogs_count' => $category->blogs_count,
            ];
        });

        // Recent posts for sidebar
        $recentPosts = Blog::published()
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($blog) use ($locale) {
                return [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'image_link' => $blog->image_link,
                    'created_at' => $blog->created_at->format('d M Y'),
                ];
            });


        return Inertia::render('Cms::BlogIndex', [
            'blogs' => $blogs,
            'categories' => $categories,
            'recentPosts' => $recentPosts,
            'filters' => [
                'search' => $request->search,
                'category' => $request->category,
            ],
        ]);
    }

    public function show($slug) {
        $blog = Blog::published()
            ->where('slug', $slug)
            ->with('category')
            ->firstOrFail();

        // Increment visits
        if (!session()->has('blog_'.$blog->id)) {
            $blog->increment('visits');
            session()->put('blog_'.$blog->id, true);
        }

        // Related blogs (same category, excluding current)
        $relatedBlogs = Blog::published()
            ->where('category_id', $blog->category_id)
            ->where('id', '!=', $blog->id)
            ->latest()
            ->limit(3)
            ->get();

        // If not enough related blogs, get recent ones
        if ($relatedBlogs->count() < 3) {
            $additionalBlogs = Blog::where('status', 'Published')
                ->where('id', '!=', $blog->id)
                ->whereNotIn('id', $relatedBlogs->pluck('id'))
                ->latest()
                ->limit(3 - $relatedBlogs->count())
                ->get();
            $relatedBlogs = $relatedBlogs->merge($additionalBlogs);
        }

        // Categories for sidebar
        $categories = BlogCategory::withCount(['blogs' => function ($q) {
            $q->where('status', 'Published');
        }])->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'blogs_count' => $category->blogs_count,
            ];
        });

        // Recent posts for sidebar
        $recentPosts = Blog::where('status', 'Published')
            ->where('id', '!=', $blog->id)
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'image_link' => $post->image_link,
                    'created_at' => $post->created_at->format('d M Y'),
                ];
            });

        // Previous and next posts
        $previousPost = Blog::where('status', 'Published')
            ->where('id', '<', $blog->id)
            ->latest('id')
            ->first();

        $nextPost = Blog::where('status', 'Published')
            ->where('id', '>', $blog->id)
            ->oldest('id')
            ->first();

        $meta = (new Meta())
            ->title($blog->title)
            ->description($blog->description)
            ->ogImage($blog->image_link)
            ->twitterImage($blog->image_link)
            ->toArray();

        return $this->inertia('Cms::BlogShow', [
            'blog' => [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'image_link' => $blog->image_link,
                'description' => $blog->description,
                'content' => $blog->content,
                'keywords' => $blog->keywords,
                'created_at' => $blog->created_at->format('d M Y'),
                'created_at_formatted' => $blog->created_at->format('d M Y'),
                'category' => $blog->category ? [
                    'id' => $blog->category->id,
                    'name' => $blog->category->name,
                    'slug' => $blog->category->slug,
                ] : null,
            ],
            'relatedBlogs' => $relatedBlogs->map(function ($blog) {
                return [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'image_link' => $blog->image_link,
                    'created_at_day' => $blog->created_at->format('d'),
                    'created_at_month' => $blog->created_at->format('M'),
                    'category' => $blog->category ? [
                        'id' => $blog->category->id,
                        'name' => $blog->category->name,
                        'slug' => $blog->category->slug,
                    ] : null,
                ];
            }),
            'categories' => $categories,
            'recentPosts' => $recentPosts,
            'previousPost' => $previousPost ? [
                'id' => $previousPost->id,
                'title' => $previousPost->title,
                'slug' => $previousPost->slug,
                'image_link' => $previousPost->image_link,
            ] : null,
            'nextPost' => $nextPost ? [
                'id' => $nextPost->id,
                'title' => $nextPost->title,
                'slug' => $nextPost->slug,
                'image_link' => $nextPost->image_link,
            ] : null,
        ], $meta);
    }
}


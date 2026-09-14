<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Base\Models\Seo;
use Modules\Base\Support\Meta;
use Modules\Base\Support\Schema;
use Modules\Cms\Models\Blog;
use Modules\Cms\Models\BlogCategory;
use Modules\SearchEngine\Models\SearchKeyword;
use Vdhicts\ReadTime\ReadTime;

class BlogController extends Controller
{
    public function index(Request $request)
    {
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

        $blogs = $query->paginate(12)->through(function ($blog) use ($locale) {
            return [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'image_link' => $blog->image_link,
                'description' => $blog->description,
                'reading_time' => $this->getReadingTimeMinutes($blog, $locale),
                'comments_count' => 0,
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
            $q->published();
        }])->get()->map(function ($category) {
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
            ->map(function ($blog) {
                return [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'image_link' => $blog->image_link,
                    'created_at' => $blog->created_at->format('d M Y'),
                ];
            });
        $siteName = Seo::get('website_name', config('app.name'));
        $canonical = route('blogs.index');
        $meta = (new Meta)
            ->title(__('Blogs').' | '.$siteName)
            ->description(__('Explore our latest blogs, insights, and technology updates.'))
            ->keywords(__('blogs, news, insights, technology trends'))
            ->ogImage()
            ->twitterImage()
            ->canonical($canonical)
            ->toArray();

        $listItems = collect($blogs->items() ?? $blogs)->take(20)->map(function ($blog) {
            return [
                'name' => is_array($blog) ? ($blog['title'] ?? '') : ($blog->title ?? ''),
                'url' => route('blogs.show', ['slug' => is_array($blog) ? $blog['slug'] : $blog->slug]),
            ];
        })->filter(fn ($item) => $item['name'] !== '' && $item['url'] !== '')->values()->all();

        return $this->inertia('Cms::BlogIndex', [
            'blogs' => $blogs,
            'categories' => $categories,
            'recentPosts' => $recentPosts,
            'filters' => [
                'search' => $request->search,
                'category' => $request->category,
            ],
            'structuredData' => [
                Schema::breadcrumbs([
                    ['name' => __('Home'), 'url' => route('home')],
                    ['name' => __('Blogs'), 'url' => $canonical],
                ]),
                Schema::itemList(__('Blogs'), $listItems, $canonical),
            ],
        ], $meta);
    }

    public function show($slug)
    {
        $locale = app()->getLocale();
        $blog = Blog::published()
            ->where('slug', $slug)
            ->with('category')
            ->firstOrFail();

        // Increment visits
        if (! session()->has('blog_'.$blog->id)) {
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
            $additionalBlogs = Blog::query()->published()
                ->where('id', '!=', $blog->id)
                ->whereNotIn('id', $relatedBlogs->pluck('id'))
                ->latest()
                ->limit(3 - $relatedBlogs->count())
                ->get();
            $relatedBlogs = $relatedBlogs->merge($additionalBlogs);
        }

        // Categories for sidebar
        $categories = BlogCategory::withCount(['blogs' => function ($q) {
            $q->published();
        }])->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'blogs_count' => $category->blogs_count,
            ];
        });

        // Recent posts for sidebar
        $recentPosts = Blog::query()->published()
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
        $previousPost = Blog::query()->published()
            ->where('id', '<', $blog->id)
            ->latest('id')
            ->first();

        $nextPost = Blog::query()->published()
            ->where('id', '>', $blog->id)
            ->oldest('id')
            ->first();

        $canonical = route('blogs.show', ['slug' => $blog->slug]);

        $meta = (new Meta)
            ->title($blog->title)
            ->description($blog->description)
            ->keywords($blog->keywords)
            ->ogImage($blog->image_link)
            ->twitterImage($blog->image_link)
            ->type('article')
            ->canonical($canonical)
            ->toArray();

        $structuredData = [
            Schema::breadcrumbs([
                ['name' => __('Home'), 'url' => route('home')],
                ['name' => __('Blogs'), 'url' => route('blogs.index')],
                ['name' => $blog->title, 'url' => $canonical],
            ]),
            Schema::article([
                'type' => 'BlogPosting',
                'title' => $blog->title,
                'description' => $blog->description,
                'image' => $blog->image_link,
                'url' => $canonical,
                'datePublished' => optional($blog->created_at)->toAtomString(),
                'dateModified' => optional($blog->updated_at)->toAtomString(),
                'keywords' => $blog->keywords,
                'section' => $blog->category?->name,
                'locale' => $locale,
            ]),
        ];

        return $this->inertia('Cms::BlogShow', [
            'structuredData' => $structuredData,
            'blog' => [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'image_link' => $blog->image_link,
                'description' => $blog->description,
                'content' => $blog->content,
                'keywords' => $blog->keywords,
                'comments_count' => 0,
                'reading_time' => $this->getReadingTimeMinutes($blog, $locale),
                'created_at' => $blog->created_at->format('d M Y'),
                'created_at_formatted' => $blog->created_at->format('d M Y'),
                'category' => $blog->category ? [
                    'id' => $blog->category->id,
                    'name' => $blog->category->name,
                    'slug' => $blog->category->slug,
                ] : null,
            ],
            'relatedBlogs' => $relatedBlogs->map(function ($blog) use ($locale) {
                return [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'image_link' => $blog->image_link,
                    'reading_time' => $this->getReadingTimeMinutes($blog, $locale),
                    'comments_count' => 0,
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

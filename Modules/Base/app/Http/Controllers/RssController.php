<?php

namespace Modules\Base\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Base\Models\Seo;
use Modules\Cms\Models\Blog;

class RssController extends Controller
{
    /**
     * Generate a dynamic RSS feed for published blog posts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locale = app()->getLocale();
        $siteName = Seo::get('website_name', config('app.name'));
        $siteDescription = Seo::get('website_desc', '');

        $posts = collect();
        try {
            if (class_exists(Blog::class)) {
                $posts = Blog::published()
                    ->latest()
                    ->limit(20)
                    ->get();
            }
        } catch (\Throwable $e) {
            // Fail silently; don't break RSS if module is missing
        }

        $items = $posts->map(function (Blog $blog) use ($locale) {
            $title = $blog->getTranslation('title', $locale) ?: $blog->title;
            $description = $blog->getTranslation('description', $locale) ?: $blog->description;
            $content = $blog->getTranslation('content', $locale) ?: $blog->content;

            if (! $description) {
                $description = Str::limit(strip_tags((string) $content), 300);
            }

            $date = $blog->updated_at ?? $blog->created_at;

            return [
                'title' => $title,
                'link' => $this->buildUrl('/blog/'.$blog->slug),
                'guid' => $this->buildUrl('/blog/'.$blog->slug),
                'pubDate' => $date ? $date->toRssString() : now()->toRssString(),
                'description' => $description,
                'content' => $content,
            ];
        });

        $lastUpdated = $posts
            ->map(fn (Blog $blog) => $blog->updated_at ?? $blog->created_at)
            ->filter()
            ->sortDesc()
            ->first();

        $content = view('rss', [
            'siteName' => $siteName,
            'siteDescription' => $siteDescription,
            'homeUrl' => $this->buildUrl('/'),
            'feedUrl' => $this->buildUrl('/rss.xml'),
            'language' => $locale,
            'lastBuildDate' => $lastUpdated ? $lastUpdated->toRssString() : now()->toRssString(),
            'items' => $items,
        ])->render();

        return response($content, 200)
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    private function buildUrl(string $path): string
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $normalizedPath = '/'.ltrim($path, '/');

        if ($normalizedPath === '/') {
            return $baseUrl.'/';
        }

        return $baseUrl.$normalizedPath;
    }
}

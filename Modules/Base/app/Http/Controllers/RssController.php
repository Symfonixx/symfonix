<?php

namespace Modules\Base\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
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
        $locale = $this->resolveLocale();
        app()->setLocale($locale);

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
            $url = $this->localizedUrl('/blog/'.$blog->slug, $locale);

            return [
                'title' => $title,
                'link' => $url,
                'guid' => $url,
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
            'homeUrl' => $this->localizedUrl('/', $locale),
            'feedUrl' => $this->host().'/rss.xml',
            'language' => $locale,
            'lastBuildDate' => $lastUpdated ? $lastUpdated->toRssString() : now()->toRssString(),
            'items' => $items,
        ])->render();

        return response($content, 200)
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    private function resolveLocale(): string
    {
        $requested = request()->query('lang');
        $supported = $this->supportedLocales();

        if (is_string($requested) && in_array($requested, $supported, true)) {
            return $requested;
        }

        if (class_exists(LaravelLocalization::class)) {
            try {
                $default = LaravelLocalization::getDefaultLocale();
                if (is_string($default) && $default !== '') {
                    return $default;
                }
            } catch (\Throwable $e) {
                // Fallback below.
            }
        }

        return (string) config('app.locale', 'en');
    }

    /**
     * @return list<string>
     */
    private function supportedLocales(): array
    {
        if (class_exists(LaravelLocalization::class)) {
            try {
                $supported = LaravelLocalization::getSupportedLocales();
                if (is_array($supported) && $supported !== []) {
                    return array_keys($supported);
                }
            } catch (\Throwable $e) {
                // Fallback below.
            }
        }

        return ['en', 'ar', 'de', 'tr'];
    }

    private function localizedUrl(string $path, string $locale): string
    {
        $normalizedPath = '/'.ltrim($path, '/');
        $absolute = $normalizedPath === '/'
            ? $this->host().'/'
            : $this->host().$normalizedPath;

        if (class_exists(LaravelLocalization::class)) {
            try {
                $url = LaravelLocalization::getLocalizedURL($locale, $absolute);
                if (is_string($url) && $url !== '') {
                    return $url;
                }
            } catch (\Throwable $e) {
                // Fallback below.
            }
        }

        if ($normalizedPath === '/') {
            return $this->host().'/'.$locale.'/';
        }

        return $this->host().'/'.$locale.$normalizedPath;
    }

    private function host(): string
    {
        return rtrim(request()->getSchemeAndHttpHost(), '/');
    }
}

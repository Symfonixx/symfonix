<?php

namespace Modules\Base\Http\Controllers;

use Illuminate\Routing\Controller;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Cms\Models\Blog;
use Modules\Cms\Models\Page;
use Modules\Services\Models\Service;

class SitemapController extends Controller
{
    /**
     * Generate a dynamic XML sitemap.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entries = [];

        // Home page
        $entries[] = [
            'path' => '/',
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // Static "About Us" page
        $entries[] = [
            'path' => '/about-us',
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.8',
        ];

        // Static "Contact Us" page
        $entries[] = [
            'path' => '/contact-us',
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.8',
        ];

        // Static informational pages
        $entries[] = [
            'path' => '/privacy-policy',
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'yearly',
            'priority' => '0.5',
        ];
        $entries[] = [
            'path' => '/team',
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.6',
        ];
        $entries[] = [
            'path' => '/testimonials',
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.6',
        ];

        // Static pages (about-us, etc.) via Page model
        try {
            if (class_exists(Page::class)) {
                Page::published()->select(['slug', 'updated_at'])->chunk(200, function ($pages) use (&$entries) {
                    foreach ($pages as $page) {
                        $entries[] = [
                            'path' => '/p/'.$page->slug,
                            'lastmod' => optional($page->updated_at)->toAtomString(),
                            'changefreq' => 'weekly',
                            'priority' => '0.7',
                        ];
                    }
                });
            }
        } catch (\Throwable $e) {
            // Fail silently; don't break sitemap if module is missing
        }

        // Blog index + posts
        try {
            if (class_exists(Blog::class)) {
                $entries[] = [
                    'path' => '/blogs',
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => 'daily',
                    'priority' => '0.9',
                ];

                Blog::published()->select(['slug', 'updated_at'])->chunk(200, function ($posts) use (&$entries) {
                    foreach ($posts as $post) {
                        $entries[] = [
                            'path' => '/blog/'.$post->slug,
                            'lastmod' => optional($post->updated_at)->toAtomString(),
                            'changefreq' => 'weekly',
                            'priority' => '0.8',
                        ];
                    }
                });
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Services index + detail
        try {
            if (class_exists(Service::class)) {
                $entries[] = [
                    'path' => '/services',
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];

                Service::published()->select(['slug', 'updated_at'])->chunk(200, function ($services) use (&$entries) {
                    foreach ($services as $service) {
                        $entries[] = [
                            'path' => '/service/'.$service->slug,
                            'lastmod' => optional($service->updated_at)->toAtomString(),
                            'changefreq' => 'weekly',
                            'priority' => '0.7',
                        ];
                    }
                });
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Products (shop) if enabled
        // try {
        //     if (class_exists(Product::class)) {
        //         Product::select(['slug', 'updated_at'])->where('status', 'Published')->chunk(200, function ($products) use (&$entries) {
        //             foreach ($products as $product) {
        //                 $entries[] = [
        //                     'path' => '/shop/' . $product->slug,
        //                     'lastmod' => optional($product->updated_at)->toAtomString(),
        //                     'changefreq' => 'weekly',
        //                     'priority' => '0.6',
        //                 ];
        //             }
        //         });
        //     }
        // } catch (\Throwable $e) {
        //     // ignore
        // }

        $urls = $this->expandLocalizedUrls($entries);

        $content = view('sitemap', ['urls' => $urls])->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
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

    private function expandLocalizedUrls(array $entries): array
    {
        $urls = [];
        $locales = $this->getSupportedLocales();

        foreach ($entries as $entry) {
            foreach ($locales as $locale) {
                $urls[] = [
                    'loc' => $this->buildLocalizedUrl($entry['path'], $locale),
                    'lastmod' => $entry['lastmod'] ?? null,
                    'changefreq' => $entry['changefreq'] ?? null,
                    'priority' => $entry['priority'] ?? null,
                ];
            }
        }

        return $urls;
    }

    private function getSupportedLocales(): array
    {
        if (class_exists(LaravelLocalization::class)) {
            try {
                $supported = LaravelLocalization::getSupportedLocales();
                if (is_array($supported) && ! empty($supported)) {
                    return array_keys($supported);
                }
            } catch (\Throwable $e) {
                // Fallback to defaults below.
            }
        }

        return ['en', 'ar', 'tr'];
    }

    private function buildLocalizedUrl(string $path, string $locale): string
    {
        $normalizedPath = '/'.ltrim($path, '/');

        if (class_exists(LaravelLocalization::class)) {
            try {
                return LaravelLocalization::getLocalizedURL($locale, $this->buildUrl($normalizedPath));
            } catch (\Throwable $e) {
                // Fallback below.
            }
        }

        $baseUrl = rtrim(config('app.url'), '/');
        $localePrefix = '/'.trim($locale, '/');
        if ($normalizedPath === '/') {
            return $baseUrl.$localePrefix.'/';
        }

return $baseUrl.$localePrefix.$normalizedPath;
    }
}
<?php

namespace Modules\Base\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Cms\Models\Page;
use Modules\Cms\Models\Blog;
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
        $urls = [];

        // Home page
        $urls[] = [
            'loc' => $this->buildUrl('/'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // Static "About Us" page
        $urls[] = [
            'loc' => $this->buildUrl('/about-us'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.8',
        ];

        // Static "Contact Us" page
        $urls[] = [
            'loc' => $this->buildUrl('/contact-us'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.8',
        ];

        // Static informational pages
        $urls[] = [
            'loc' => $this->buildUrl('/privacy-policy'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'yearly',
            'priority' => '0.5',
        ];
        $urls[] = [
            'loc' => $this->buildUrl('/team'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.6',
        ];
        $urls[] = [
            'loc' => $this->buildUrl('/testimonials'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.6',
        ];

        // Static pages (about-us, etc.) via Page model
        try {
            if (class_exists(Page::class)) {
                Page::published()->select(['slug', 'updated_at'])->chunk(200, function ($pages) use (&$urls) {
                    foreach ($pages as $page) {
                        $urls[] = [
                            'loc' => $this->buildUrl('/p/' . $page->slug),
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
                $urls[] = [
                    'loc' => $this->buildUrl('/blogs'),
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => 'daily',
                    'priority' => '0.9',
                ];

                Blog::published()->select(['slug', 'updated_at'])->chunk(200, function ($posts) use (&$urls) {
                    foreach ($posts as $post) {
                        $urls[] = [
                            'loc' => $this->buildUrl('/blog/' . $post->slug),
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
                $urls[] = [
                    'loc' => $this->buildUrl('/services'),
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];

                Service::published()->select(['slug', 'updated_at'])->chunk(200, function ($services) use (&$urls) {
                    foreach ($services as $service) {
                        $urls[] = [
                            'loc' => $this->buildUrl('/service/' . $service->slug),
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
        //         Product::select(['slug', 'updated_at'])->where('status', 'Published')->chunk(200, function ($products) use (&$urls) {
        //             foreach ($products as $product) {
        //                 $urls[] = [
        //                     'loc' => URL::to('/shop/' . $product->slug),
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

        $content = view('sitemap', ['urls' => $urls])->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function buildUrl(string $path): string
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $normalizedPath = '/' . ltrim($path, '/');

        if ($normalizedPath === '/') {
            return $baseUrl . '/';
        }

        return $baseUrl . $normalizedPath;
    }
}



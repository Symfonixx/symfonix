<?php

namespace Modules\Base\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Modules\Cms\Models\Page;
use Modules\Cms\Models\Blog;
use Modules\Services\Models\Service;
use Modules\Shop\Models\Product;

class SitemapController extends Controller
{
    /**
     * Generate a dynamic XML sitemap.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locale = App::getLocale();

        $urls = [];

        // Home page
        $urls[] = [
            'loc' => URL::to('/'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // Static "About Us" page
        $urls[] = [
            'loc' => URL::to('/about-us'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.8',
        ];

        // Static "Contact Us" page
        $urls[] = [
            'loc' => URL::to('/contact-us'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.8',
        ];

        // Static pages (about-us, etc.) via Page model
        try {
            if (class_exists(Page::class)) {
                Page::published()->select(['slug', 'updated_at'])->chunk(200, function ($pages) use (&$urls) {
                    foreach ($pages as $page) {
                        $urls[] = [
                            'loc' => URL::to('/p/' . $page->slug),
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
                    'loc' => URL::to('/blogs'),
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => 'daily',
                    'priority' => '0.9',
                ];

                Blog::published()->select(['slug', 'updated_at'])->chunk(200, function ($posts) use (&$urls) {
                    foreach ($posts as $post) {
                        $urls[] = [
                            'loc' => URL::to('/blog/' . $post->slug),
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
                    'loc' => URL::to('/services'),
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];

                Service::published()->select(['slug', 'updated_at'])->chunk(200, function ($services) use (&$urls) {
                    foreach ($services as $service) {
                        $urls[] = [
                            'loc' => URL::to('/service/' . $service->slug),
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
}



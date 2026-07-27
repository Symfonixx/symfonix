<?php

namespace Modules\Base\Http\Controllers;

use DOMDocument;
use Illuminate\Routing\Controller;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Cms\Models\Blog;
use Modules\Cms\Models\Page;
use Modules\Product\Models\Product;
use Modules\Project\Models\ProjectUseCase;
use Modules\Services\Models\Service;
use Symfony\Component\HttpFoundation\Response;

class SitemapController extends Controller
{
    private const SITEMAP_NS = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    private const XHTML_NS = 'http://www.w3.org/1999/xhtml';
    /**
     * Generate a dynamic XML sitemap.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        if (app()->bound('debugbar')) {
            app('debugbar')->disable();
        }

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
        $entries[] = [
            'path' => '/faq',
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

        // Case studies (use cases) index + detail
        try {
            if (class_exists(ProjectUseCase::class)) {
                $entries[] = [
                    'path' => '/use-cases',
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];

                ProjectUseCase::published()->select(['slug', 'updated_at'])->chunk(200, function ($useCases) use (&$entries) {
                    foreach ($useCases as $useCase) {
                        $entries[] = [
                            'path' => '/use-cases/'.$useCase->slug,
                            'lastmod' => optional($useCase->updated_at)->toAtomString(),
                            'changefreq' => 'weekly',
                            'priority' => '0.7',
                        ];
                    }
                });
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Products index + detail
        try {
            if (class_exists(Product::class)) {
                $entries[] = [
                    'path' => '/products',
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];

                Product::published()->active()->select(['slug', 'updated_at'])->chunk(200, function ($products) use (&$entries) {
                    foreach ($products as $product) {
                        $entries[] = [
                            'path' => '/products/'.$product->slug,
                            'lastmod' => optional($product->updated_at)->toAtomString(),
                            'changefreq' => 'weekly',
                            'priority' => '0.7',
                        ];
                    }
                });
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $urls = $this->expandLocalizedUrls($entries);

        $response = response($this->buildXml($urls), Response::HTTP_OK);
        // text/xml is the Sitemap protocol preference; nosniff stops browsers
        // from reinterpreting this as HTML (which hides tags and concatenates text).
        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');
        $response->headers->set('Cache-Control', 'public, max-age=3600');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }

    /**
     * @param  array<int, array{loc: string, lastmod?: string|null, changefreq?: string|null, priority?: string|null, alternates?: array<int, array{hreflang: string, href: string}>}>  $urls
     */
    private function buildXml(array $urls): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $urlset = $dom->createElementNS(self::SITEMAP_NS, 'urlset');
        $urlset->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xhtml', self::XHTML_NS);
        $dom->appendChild($urlset);

        foreach ($urls as $url) {
            $urlNode = $dom->createElementNS(self::SITEMAP_NS, 'url');
            $urlset->appendChild($urlNode);

            $urlNode->appendChild($this->domTextElement($dom, self::SITEMAP_NS, 'loc', $url['loc']));

            if (! empty($url['lastmod'])) {
                $urlNode->appendChild($this->domTextElement($dom, self::SITEMAP_NS, 'lastmod', $url['lastmod']));
            }

            if (! empty($url['changefreq'])) {
                $urlNode->appendChild($this->domTextElement($dom, self::SITEMAP_NS, 'changefreq', $url['changefreq']));
            }

            if (! empty($url['priority'])) {
                $urlNode->appendChild($this->domTextElement($dom, self::SITEMAP_NS, 'priority', $url['priority']));
            }

            foreach ($url['alternates'] ?? [] as $alternate) {
                $link = $dom->createElementNS(self::XHTML_NS, 'xhtml:link');
                $link->setAttribute('rel', 'alternate');
                $link->setAttribute('hreflang', $alternate['hreflang']);
                $link->setAttribute('href', $alternate['href']);
                $urlNode->appendChild($link);
            }
        }

        $xml = $dom->saveXML();

        return is_string($xml) ? $xml : '';
    }

    private function domTextElement(DOMDocument $dom, string $namespace, string $name, string $value): \DOMElement
    {
        $element = $dom->createElementNS($namespace, $name);
        $element->appendChild($dom->createTextNode($value));

        return $element;
    }

    private function buildUrl(string $path): string
    {
        $baseUrl = rtrim(request()->getSchemeAndHttpHost(), '/');
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
        $defaultLocale = $this->getDefaultLocale();

        foreach ($entries as $entry) {
            // Build the shared set of hreflang alternates for this path once.
            $alternates = [];
            foreach ($locales as $locale) {
                $alternates[] = [
                    'hreflang' => $locale,
                    'href' => $this->buildLocalizedUrl($entry['path'], $locale),
                ];
            }
            $alternates[] = [
                'hreflang' => 'x-default',
                'href' => $this->buildLocalizedUrl($entry['path'], $defaultLocale),
            ];

            foreach ($locales as $locale) {
                $urls[] = [
                    'loc' => $this->buildLocalizedUrl($entry['path'], $locale),
                    'lastmod' => $entry['lastmod'] ?? null,
                    'changefreq' => $entry['changefreq'] ?? null,
                    'priority' => $entry['priority'] ?? null,
                    'alternates' => $alternates,
                ];
            }
        }

        return $urls;
    }

    private function getDefaultLocale(): string
    {
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

        return 'en';
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

        return ['en', 'ar', 'de', 'tr'];
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

        $baseUrl = rtrim(request()->getSchemeAndHttpHost(), '/');
        $localePrefix = '/'.trim($locale, '/');
        if ($normalizedPath === '/') {
            return $baseUrl.$localePrefix.'/';
        }

        return $baseUrl.$localePrefix.$normalizedPath;
    }
}



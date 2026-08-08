<?php

namespace Modules\Base\Support;

use Modules\Base\Models\Seo;
use Modules\Base\Models\Settings;

/**
 * Builds Schema.org JSON-LD structured data graphs for SEO rich results.
 *
 * Every method returns a self-contained array ready to be json_encoded into a
 * <script type="application/ld+json"> tag. Collect them into an array and pass
 * them to the Inertia response as the `structuredData` prop; the root Blade
 * template renders each one server-side (important for crawlers).
 */
class Schema
{
    /**
     * BreadcrumbList schema.
     *
     * @param  array<int, array{name: string, url: string|null}>  $items
     */
    public static function breadcrumbs(array $items): array
    {
        $position = 1;
        $elements = [];

        foreach ($items as $item) {
            if (empty($item['name'])) {
                continue;
            }

            $element = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $item['name'],
            ];

            if (! empty($item['url'])) {
                $element['item'] = $item['url'];
            }

            $elements[] = $element;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $elements,
        ];
    }

    /**
     * Article / BlogPosting schema for editorial detail pages.
     */
    public static function article(array $data): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => $data['type'] ?? 'Article',
            'headline' => $data['title'] ?? null,
            'description' => self::plain($data['description'] ?? null),
            'image' => ! empty($data['image']) ? [$data['image']] : null,
            'datePublished' => $data['datePublished'] ?? null,
            'dateModified' => $data['dateModified'] ?? $data['datePublished'] ?? null,
            'inLanguage' => $data['locale'] ?? app()->getLocale(),
            'mainEntityOfPage' => ! empty($data['url']) ? [
                '@type' => 'WebPage',
                '@id' => $data['url'],
            ] : null,
            'articleSection' => $data['section'] ?? null,
            'keywords' => $data['keywords'] ?? null,
            'author' => self::publisher(),
            'publisher' => self::publisher(),
        ], fn ($v) => ! is_null($v) && $v !== '' && $v !== []);
    }

    /**
     * Service schema for service detail pages.
     */
    public static function service(array $data): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $data['title'] ?? null,
            'description' => self::plain($data['description'] ?? null),
            'image' => $data['image'] ?? null,
            'serviceType' => $data['category'] ?? null,
            'url' => $data['url'] ?? null,
            'inLanguage' => $data['locale'] ?? app()->getLocale(),
            'provider' => self::publisher(),
            'areaServed' => $data['areaServed'] ?? null,
        ], fn ($v) => ! is_null($v) && $v !== '' && $v !== []);
    }

    /**
     * Product schema for product detail pages.
     *
     * Google Product rich results require offers, review, or aggregateRating.
     */
    public static function product(array $data): array
    {
        $product = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $data['name'] ?? null,
            'description' => self::plain($data['description'] ?? null),
            'image' => ! empty($data['image']) ? [$data['image']] : null,
            'sku' => $data['sku'] ?? null,
            'category' => $data['category'] ?? null,
            'url' => $data['url'] ?? null,
            'brand' => [
                '@type' => 'Brand',
                'name' => Seo::get('website_name', config('app.name')),
            ],
        ], fn ($v) => ! is_null($v) && $v !== '' && $v !== []);

        // Price "0" must still emit offers — empty() would incorrectly skip it.
        if (array_key_exists('price', $data) && $data['price'] !== null && $data['price'] !== '') {
            $product['offers'] = array_filter([
                '@type' => 'Offer',
                'price' => number_format((float) $data['price'], 2, '.', ''),
                'priceCurrency' => $data['currency'] ?? 'USD',
                'availability' => $data['availability'] ?? 'https://schema.org/InStock',
                'url' => $data['url'] ?? null,
                'seller' => self::publisher(),
            ], fn ($v) => ! is_null($v) && $v !== '' && $v !== []);
        }

        return $product;
    }

    /**
     * WebPage schema for marketing / CMS pages.
     */
    public static function webPage(array $data): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => $data['type'] ?? 'WebPage',
            'name' => $data['title'] ?? null,
            'description' => self::plain($data['description'] ?? null),
            'url' => $data['url'] ?? null,
            'inLanguage' => $data['locale'] ?? app()->getLocale(),
            'isPartOf' => [
                '@type' => 'WebSite',
                '@id' => rtrim(config('app.url') ?: url('/'), '/').'/#website',
            ],
            'about' => self::publisher(),
        ], fn ($v) => ! is_null($v) && $v !== '' && $v !== []);
    }

    /**
     * ItemList schema for catalog / index pages.
     *
     * @param  array<int, array{name: string, url: string}>  $items
     */
    public static function itemList(string $name, array $items, ?string $url = null): array
    {
        $elements = [];
        $position = 1;

        foreach ($items as $item) {
            if (empty($item['name']) || empty($item['url'])) {
                continue;
            }

            $elements[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $item['name'],
                'url' => $item['url'],
            ];
        }

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $name,
            'url' => $url,
            'numberOfItems' => count($elements),
            'itemListElement' => $elements,
        ], fn ($v) => ! is_null($v) && $v !== '' && $v !== []);
    }

    /**
     * Shared Organization node used as author/publisher/provider.
     */
    protected static function publisher(): array
    {
        $name = Seo::get('website_name', config('app.name'));
        $siteUrl = rtrim(config('app.url') ?: url('/'), '/');
        $logo = Settings::get('site_logo');

        return array_filter([
            '@type' => 'Organization',
            '@id' => $siteUrl.'/#organization',
            'name' => $name,
            'url' => $siteUrl,
            'logo' => $logo ? asset('storage/'.$logo) : null,
        ], fn ($v) => ! is_null($v) && $v !== '');
    }

    /**
     * Strip HTML and collapse whitespace for schema text fields.
     */
    protected static function plain(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $text = trim(preg_replace('/\s+/', ' ', strip_tags($value)));

        return $text !== '' ? mb_substr($text, 0, 5000) : null;
    }
}

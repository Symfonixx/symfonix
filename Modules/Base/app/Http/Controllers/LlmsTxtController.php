<?php

namespace Modules\Base\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Base\Models\Seo;
use Modules\Cms\Models\Blog;
use Modules\Cms\Models\Page;
use Modules\Product\Models\Product;
use Modules\Project\Models\ProjectUseCase;
use Modules\Services\Models\Service;

class LlmsTxtController extends Controller
{
    /**
     * Curated multilingual llms.txt index for AI agents.
     */
    public function index(): Response
    {
        $defaultLocale = $this->defaultLocale();
        $this->useLocale($defaultLocale);

        $siteName = $this->siteName($defaultLocale);
        $siteDescription = $this->siteDescription($defaultLocale);
        $host = $this->host();
        $locales = $this->locales();
        $localeLabels = $this->localeLabels();

        $lines = [
            '# '.$siteName,
            '',
            '> '.$siteDescription,
            '',
            $siteName.' designs and scales digital systems across web, mobile, AI, and cloud.',
            'This file lists public pages in all supported languages: '.implode(', ', array_map(
                fn (string $locale) => ($localeLabels[$locale] ?? strtoupper($locale)).' ('.$locale.')',
                $locales
            )).'.',
            'Prefer these URLs over scraping the whole site. Admin, customer portal, and authentication areas are private.',
            '',
            '## Languages',
            '',
        ];

        foreach ($locales as $locale) {
            $label = $localeLabels[$locale] ?? strtoupper($locale);
            $lines[] = $this->linkLine(
                $siteName.' — '.$label,
                '/',
                'Homepage in '.$label,
                $locale
            );
        }

        $lines[] = '';
        $lines[] = '## Pages';
        $lines[] = '';

        foreach ($this->staticPages() as $page) {
            foreach ($locales as $locale) {
                $lines[] = $this->linkLine(
                    $page['title'].' ('.$locale.')',
                    $page['path'],
                    $page['note'] ?? null,
                    $locale
                );
            }
        }

        $services = $this->serviceModels();

        $lines[] = '';
        $lines[] = '## Services';
        $lines[] = '';

        foreach ($locales as $locale) {
            $lines[] = $this->linkLine(
                'All services ('.$locale.')',
                '/services',
                'Overview of Symfonix service offerings',
                $locale
            );
        }

        foreach ($services as $service) {
            foreach ($locales as $locale) {
                $title = $this->translation($service, 'title', $locale) ?: 'Service';
                $note = $this->plainSummary($this->translation($service, 'description', $locale));

                $lines[] = $this->linkLine(
                    $title.' ('.$locale.')',
                    '/service/'.$service->slug,
                    $note !== '' ? $note : null,
                    $locale
                );
            }
        }

        $products = $this->productModels();

        $lines[] = '';
        $lines[] = '## Products';
        $lines[] = '';

        foreach ($locales as $locale) {
            $lines[] = $this->linkLine(
                'All products ('.$locale.')',
                '/products',
                'Product catalog',
                $locale
            );
        }

        foreach ($products as $product) {
            foreach ($locales as $locale) {
                $title = $this->translation($product, 'name', $locale) ?: 'Product';
                $note = $this->plainSummary(
                    $this->translation($product, 'short_description', $locale)
                        ?: $product->seoDescription($locale)
                );

                $lines[] = $this->linkLine(
                    $title.' ('.$locale.')',
                    '/products/'.$product->slug,
                    $note !== '' ? $note : null,
                    $locale
                );
            }
        }

        $useCases = $this->useCaseModels();

        $lines[] = '';
        $lines[] = '## Case studies';
        $lines[] = '';

        foreach ($locales as $locale) {
            $lines[] = $this->linkLine(
                'All use cases ('.$locale.')',
                '/use-cases',
                'Client case studies and project outcomes',
                $locale
            );
        }

        foreach ($useCases as $useCase) {
            foreach ($locales as $locale) {
                $title = $this->translation($useCase, 'title', $locale) ?: 'Use case';
                $note = $this->plainSummary($this->translation($useCase, 'summary', $locale));

                $lines[] = $this->linkLine(
                    $title.' ('.$locale.')',
                    '/use-cases/'.$useCase->slug,
                    $note !== '' ? $note : null,
                    $locale
                );
            }
        }

        $cmsPages = $this->cmsPageModels();

        if ($cmsPages->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '## CMS pages';
            $lines[] = '';

            foreach ($cmsPages as $page) {
                foreach ($locales as $locale) {
                    $title = $this->translation($page, 'title', $locale) ?: 'Page';
                    $note = $this->plainSummary($this->translation($page, 'description', $locale));

                    $lines[] = $this->linkLine(
                        $title.' ('.$locale.')',
                        '/p/'.$page->slug,
                        $note !== '' ? $note : null,
                        $locale
                    );
                }
            }
        }

        $blogs = $this->blogModels(25);

        $lines[] = '';
        $lines[] = '## Optional';
        $lines[] = '';

        foreach ($locales as $locale) {
            $lines[] = $this->linkLine(
                'Blog ('.$locale.')',
                '/blogs',
                'Articles and insights',
                $locale
            );
        }

        foreach ($blogs as $blog) {
            foreach ($locales as $locale) {
                $title = $this->translation($blog, 'title', $locale) ?: 'Blog post';
                $note = $this->plainSummary($this->translation($blog, 'description', $locale));

                $lines[] = $this->linkLine(
                    $title.' ('.$locale.')',
                    '/blog/'.$blog->slug,
                    $note !== '' ? $note : null,
                    $locale
                );
            }
        }

        $lines[] = '- [Full content dump]('.$host.'/llms-full.txt): Markdown of key public pages in all supported languages';
        $lines[] = '- [Sitemap]('.$host.'/sitemap.xml): Complete localized URL list for crawlers';
        $lines[] = '- [RSS]('.$host.'/rss.xml): Latest blog posts feed';
        $lines[] = '';

        return $this->plainText(implode("\n", $lines));
    }

    /**
     * Multilingual llms-full.txt with concatenated public page content.
     */
    public function full(): Response
    {
        $defaultLocale = $this->defaultLocale();
        $this->useLocale($defaultLocale);

        $siteName = $this->siteName($defaultLocale);
        $siteDescription = $this->siteDescription($defaultLocale);
        $locales = $this->locales();
        $localeLabels = $this->localeLabels();
        $sections = [];

        $sections[] = $this->section(
            $siteName,
            $this->localizedUrl('/', $defaultLocale),
            $siteDescription."\n\n"
            .$siteName.' designs and scales digital systems across web, mobile, AI, and cloud. '
            .'This file consolidates public content for AI agents in: '
            .implode(', ', array_map(fn (string $locale) => $localeLabels[$locale] ?? $locale, $locales)).'.'
        );

        foreach ($this->staticPages() as $page) {
            foreach ($locales as $locale) {
                $sections[] = $this->section(
                    $page['title'].' ('.$locale.')',
                    $this->localizedUrl($page['path'], $locale),
                    $page['note'] ?? ''
                );
            }
        }

        foreach ($this->serviceModels() as $service) {
            foreach ($locales as $locale) {
                $title = $this->translation($service, 'title', $locale) ?: 'Service';
                $body = $this->composeBody([
                    $this->plainSummary($this->translation($service, 'description', $locale)),
                    $this->htmlToText($this->translation($service, 'content', $locale)),
                ]);

                $sections[] = $this->section(
                    $title.' ('.$locale.')',
                    $this->localizedUrl('/service/'.$service->slug, $locale),
                    $body
                );
            }
        }

        foreach ($this->productModels() as $product) {
            foreach ($locales as $locale) {
                $title = $this->translation($product, 'name', $locale) ?: 'Product';
                $description = $this->plainSummary(
                    $this->translation($product, 'short_description', $locale)
                        ?: $product->seoDescription($locale)
                );
                $body = $this->composeBody([
                    $description,
                    $this->htmlToText($this->translation($product, 'description', $locale)),
                ]);

                $sections[] = $this->section(
                    $title.' ('.$locale.')',
                    $this->localizedUrl('/products/'.$product->slug, $locale),
                    $body
                );
            }
        }

        foreach ($this->useCaseModels() as $useCase) {
            foreach ($locales as $locale) {
                $title = $this->translation($useCase, 'title', $locale) ?: 'Use case';
                $body = $this->composeBody([
                    $this->plainSummary($this->translation($useCase, 'summary', $locale)),
                    $this->labeledBlock('Challenge', $this->htmlToText($this->translation($useCase, 'challenge', $locale))),
                    $this->labeledBlock('Solution', $this->htmlToText($this->translation($useCase, 'solution', $locale))),
                    $this->labeledBlock('Results', $this->htmlToText($this->translation($useCase, 'results', $locale))),
                    $this->htmlToText($this->translation($useCase, 'content', $locale)),
                ]);

                $sections[] = $this->section(
                    $title.' ('.$locale.')',
                    $this->localizedUrl('/use-cases/'.$useCase->slug, $locale),
                    $body
                );
            }
        }

        foreach ($this->cmsPageModels() as $page) {
            foreach ($locales as $locale) {
                $title = $this->translation($page, 'title', $locale) ?: 'Page';
                $body = $this->composeBody([
                    $this->plainSummary($this->translation($page, 'description', $locale)),
                    $this->htmlToText($this->translation($page, 'content', $locale)),
                ]);

                $sections[] = $this->section(
                    $title.' ('.$locale.')',
                    $this->localizedUrl('/p/'.$page->slug, $locale),
                    $body
                );
            }
        }

        foreach ($this->blogModels(50) as $blog) {
            foreach ($locales as $locale) {
                $title = $this->translation($blog, 'title', $locale) ?: 'Blog post';
                $body = $this->composeBody([
                    $this->plainSummary($this->translation($blog, 'description', $locale)),
                    $this->htmlToText($this->translation($blog, 'content', $locale)),
                ]);

                $sections[] = $this->section(
                    $title.' ('.$locale.')',
                    $this->localizedUrl('/blog/'.$blog->slug, $locale),
                    $body
                );
            }
        }

        $content = '# '.$siteName." — Full content\n\n"
            .'> '.$siteDescription."\n\n"
            ."Generated dynamically from published public content in all supported languages.\n\n"
            .implode("\n\n---\n\n", array_filter($sections));

        return $this->plainText($content."\n");
    }

    /**
     * @return list<string>
     */
    private function locales(): array
    {
        $supported = [];

        if (class_exists(LaravelLocalization::class)) {
            try {
                $configured = LaravelLocalization::getSupportedLocales();
                if (is_array($configured) && $configured !== []) {
                    $supported = array_keys($configured);
                }
            } catch (\Throwable $e) {
                // Fallback below.
            }
        }

        if ($supported === []) {
            $supported = array_keys(config('laravellocalization.supportedLocales', [
                'en' => [],
                'ar' => [],
                'de' => [],
                'tr' => [],
            ]));
        }

        $default = $this->defaultLocale();

        return array_values(array_unique(array_merge([$default], $supported)));
    }

    /**
     * @return array<string, string>
     */
    private function localeLabels(): array
    {
        $labels = [];

        if (class_exists(LaravelLocalization::class)) {
            try {
                foreach (LaravelLocalization::getSupportedLocales() as $code => $meta) {
                    $labels[$code] = (string) ($meta['name'] ?? $meta['native'] ?? strtoupper($code));
                }
            } catch (\Throwable $e) {
                // Fallback below.
            }
        }

        return $labels + [
            'en' => 'English',
            'ar' => 'Arabic',
            'de' => 'German',
            'tr' => 'Turkish',
        ];
    }

    private function defaultLocale(): string
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

        return (string) config('app.locale', 'en');
    }

    private function useLocale(string $locale): void
    {
        app()->setLocale($locale);

        if (class_exists(LaravelLocalization::class)) {
            try {
                LaravelLocalization::setLocale($locale);
            } catch (\Throwable $e) {
                // Keep app locale even if localization helper fails.
            }
        }
    }

    private function siteName(string $locale): string
    {
        return $this->seoValue('website_name', $locale, (string) config('app.name', 'Symfonix')) ?: 'Symfonix';
    }

    private function siteDescription(string $locale): string
    {
        $description = $this->seoValue('website_desc', $locale, '');

        if ($description !== '') {
            return $description;
        }

        return 'Technology company building web, mobile, AI, and cloud solutions.';
    }

    private function seoValue(string $key, string $locale, string $default = ''): string
    {
        try {
            $previous = app()->getLocale();
            app()->setLocale($locale);
            $value = trim((string) Seo::get($key, $default));
            app()->setLocale($previous);

            if ($value !== '') {
                return $value;
            }
        } catch (\Throwable $e) {
            // Fallback below.
        }

        return $default;
    }

    private function host(): string
    {
        return rtrim(request()->getSchemeAndHttpHost(), '/');
    }

    /**
     * @return list<array{title: string, path: string, note?: string}>
     */
    private function staticPages(): array
    {
        return [
            ['title' => 'Home', 'path' => '/', 'note' => 'Company overview and highlights'],
            ['title' => 'About us', 'path' => '/about-us', 'note' => 'Mission, approach, and company background'],
            ['title' => 'Services', 'path' => '/services', 'note' => 'Service catalog'],
            ['title' => 'Products', 'path' => '/products', 'note' => 'Product catalog'],
            ['title' => 'Use cases', 'path' => '/use-cases', 'note' => 'Case studies'],
            ['title' => 'Blog', 'path' => '/blogs', 'note' => 'Insights and articles'],
            ['title' => 'Team', 'path' => '/team', 'note' => 'People behind Symfonix'],
            ['title' => 'Testimonials', 'path' => '/testimonials', 'note' => 'Client feedback'],
            ['title' => 'FAQ', 'path' => '/faq', 'note' => 'Frequently asked questions'],
            ['title' => 'Contact us', 'path' => '/contact-us', 'note' => 'Get in touch'],
            ['title' => 'Privacy policy', 'path' => '/privacy-policy', 'note' => 'Privacy and data practices'],
        ];
    }

    /**
     * @return \Illuminate\Support\Collection<int, Service>
     */
    private function serviceModels()
    {
        try {
            if (! class_exists(Service::class)) {
                return collect();
            }

            return Service::published()
                ->orderByDesc('featured')
                ->orderBy('id')
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, Product>
     */
    private function productModels()
    {
        try {
            if (! class_exists(Product::class)) {
                return collect();
            }

            return Product::published()
                ->active()
                ->orderByDesc('is_featured')
                ->orderBy('id')
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, ProjectUseCase>
     */
    private function useCaseModels()
    {
        try {
            if (! class_exists(ProjectUseCase::class)) {
                return collect();
            }

            return ProjectUseCase::published()
                ->ordered()
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, Page>
     */
    private function cmsPageModels()
    {
        try {
            if (! class_exists(Page::class)) {
                return collect();
            }

            return Page::published()
                ->orderBy('id')
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, Blog>
     */
    private function blogModels(int $limit = 25)
    {
        try {
            if (! class_exists(Blog::class)) {
                return collect();
            }

            return Blog::published()
                ->latest()
                ->limit($limit)
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private function translation(object $model, string $field, string $locale): string
    {
        if (! method_exists($model, 'getTranslation')) {
            $value = $model->{$field} ?? '';

            return is_string($value) ? $value : '';
        }

        $value = $model->getTranslation($field, $locale, false);

        if (! is_string($value) || trim($value) === '') {
            $value = $model->getTranslation($field, $this->defaultLocale(), false);
        }

        if (! is_string($value) || trim($value) === '') {
            $value = $model->getTranslation($field, $locale, true);
        }

        return is_string($value) ? $value : '';
    }

    private function linkLine(string $title, string $path, ?string $note = null, ?string $locale = null): string
    {
        $line = '- ['.$this->escapeMarkdownLabel($title).']('.$this->localizedUrl($path, $locale).')';

        if ($note) {
            $line .= ': '.$this->oneLine($note);
        }

        return $line;
    }

    private function localizedUrl(string $path, ?string $locale = null): string
    {
        $locale = $locale ?: $this->defaultLocale();
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

    private function section(string $title, string $url, string $body): string
    {
        $parts = [
            '## '.$title,
            '',
            'Source: '.$url,
        ];

        $cleanBody = trim($body);
        if ($cleanBody !== '') {
            $parts[] = '';
            $parts[] = $cleanBody;
        }

        return implode("\n", $parts);
    }

    /**
     * @param  list<string|null>  $parts
     */
    private function composeBody(array $parts): string
    {
        return collect($parts)
            ->map(fn ($part) => trim((string) $part))
            ->filter()
            ->implode("\n\n");
    }

    private function labeledBlock(string $label, string $content): string
    {
        $content = trim($content);

        if ($content === '') {
            return '';
        }

        return '### '.$label."\n\n".$content;
    }

    private function plainSummary(?string $value): string
    {
        return $this->oneLine($this->htmlToText((string) $value));
    }

    private function oneLine(string $value): string
    {
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? '';

        return Str::limit($value, 160, '…');
    }

    private function htmlToText(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $text = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/<\s*br\s*\/?\s*>/i', "\n", $text) ?? $text;
        $text = preg_replace('/<\s*\/\s*p\s*>/i', "\n\n", $text) ?? $text;
        $text = preg_replace('/<\s*\/\s*h[1-6]\s*>/i', "\n\n", $text) ?? $text;
        $text = preg_replace('/<\s*li[^>]*>/i', '- ', $text) ?? $text;
        $text = preg_replace('/<\s*\/\s*li\s*>/i', "\n", $text) ?? $text;
        $text = strip_tags($text);
        $text = preg_replace("/[ \t]+/u", ' ', $text) ?? $text;
        $text = preg_replace("/\n{3,}/u", "\n\n", $text) ?? $text;

        return trim($text);
    }

    private function escapeMarkdownLabel(string $label): string
    {
        return str_replace(['[', ']'], ['\\[', '\\]'], $label);
    }

    private function plainText(string $content): Response
    {
        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=300',
        ]);
    }
}

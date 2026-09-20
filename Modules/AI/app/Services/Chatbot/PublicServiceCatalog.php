<?php

namespace Modules\AI\Services\Chatbot;

use Illuminate\Database\Eloquent\Builder;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\AI\Support\CostLimiter;
use Modules\Services\Models\Service;

class PublicServiceCatalog
{
    /**
     * @var array<string, list<string>>
     */
    private const QUERY_ALIASES = [
        'web' => ['web', 'website', 'frontend', 'backend', 'laravel', 'php', 'ويب', 'موقع', 'مواقع'],
        'mobile' => ['mobile', 'ios', 'android', 'app', 'flutter', 'تطبيق', 'موبايل', 'جوال'],
        'cloud' => ['cloud', 'aws', 'azure', 'devops', 'سحاب', 'كلاود', 'خوادم'],
        'ai' => ['ai', 'ml', 'llm', 'automat', 'ذكاء', 'أتمتة', 'اتمت'],
        'marketing' => ['market', 'seo', 'social', 'تسويق'],
        'brand' => ['brand', 'identity', 'ui', 'ux', 'علامة', 'هوية'],
    ];

    public function __construct(private readonly CostLimiter $limiter) {}

    /**
     * @return list<array<string, mixed>>
     */
    public function list(?string $query = null): array
    {
        $services = $this->publishedQuery()
            ->with('category')
            ->orderByDesc('featured')
            ->orderBy('id')
            ->get();

        $items = [];

        foreach ($services as $service) {
            $row = $this->toArray($service, false);
            $row['_score'] = $this->score($service, $query);
            $items[] = $row;
        }

        usort($items, function (array $left, array $right): int {
            return ($right['_score'] <=> $left['_score']) ?: (($left['id'] ?? 0) <=> ($right['id'] ?? 0));
        });

        return array_map(function (array $row): array {
            unset($row['_score']);

            return $row;
        }, $this->limiter->limitList($items));
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(?int $id = null, ?string $slug = null, ?string $query = null): ?array
    {
        if ($id !== null && $id > 0) {
            $service = $this->publishedQuery()->with('category')->find($id);

            return $service ? $this->toArray($service, true) : null;
        }

        if (is_string($slug) && $slug !== '') {
            $service = $this->publishedQuery()->with('category')->where('slug', $slug)->first();

            return $service ? $this->toArray($service, true) : null;
        }

        $needle = $this->normalize($query);
        if ($needle === '') {
            return null;
        }

        foreach ($this->list($query) as $item) {
            $service = $this->publishedQuery()->with('category')->find((int) ($item['id'] ?? 0));
            if ($service && $this->score($service, $query) > 0) {
                return $this->toArray($service, true);
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Service $service, bool $detailed = false): array
    {
        $locale = app()->getLocale();
        $title = $this->translated($service, 'title', $locale);
        $description = $this->translated($service, 'description', $locale);
        $payload = [
            'id' => $service->id,
            'slug' => $service->slug,
            'title' => $title,
            'category' => $service->category
                ? $this->plain($service->category->getTranslation('title', $locale) ?: $service->category->getTranslation('title', 'en'))
                : null,
            'summary' => $this->limiter->truncateString($description) ?? '',
            'featured' => (bool) $service->featured,
            'url' => $this->publicUrl((string) $service->slug),
        ];

        if ($detailed) {
            $content = $this->translated($service, 'content', $locale, 800);
            $payload['details'] = $this->limiter->truncateString($content !== '' ? $content : $description);
            $payload['keywords'] = $this->translated($service, 'keywords', $locale, 200);
        }

        return $payload;
    }

    private function publishedQuery(): Builder
    {
        return Service::query()->published();
    }

    private function publicUrl(string $slug): string
    {
        try {
            $path = route('services.show', ['slug' => $slug], false);

            return LaravelLocalization::getLocalizedURL(app()->getLocale(), $path) ?: url($path);
        } catch (\Throwable) {
            return url('/service/'.$slug);
        }
    }

    private function score(Service $service, ?string $query): int
    {
        $needles = $this->searchNeedles($query);
        if ($needles === []) {
            return (int) $service->featured;
        }

        $haystack = $this->searchBlob($service);
        $score = (int) $service->featured;

        foreach ($needles as $needle) {
            if ($needle !== '' && str_contains($haystack, $needle)) {
                $score += 10;
            }
        }

        return $score;
    }

    /**
     * @return list<string>
     */
    private function searchNeedles(?string $query): array
    {
        $needle = $this->normalize($query);
        if ($needle === '') {
            return [];
        }

        $needles = [$needle];

        foreach (self::QUERY_ALIASES as $aliases) {
            foreach ($aliases as $alias) {
                if (str_contains($needle, $alias)) {
                    array_push($needles, ...$aliases);
                    break;
                }
            }
        }

        return array_values(array_unique(array_filter($needles)));
    }

    private function searchBlob(Service $service): string
    {
        $parts = [
            (string) $service->slug,
        ];

        foreach (['title', 'description', 'keywords'] as $field) {
            $translations = $service->getTranslations($field);
            if (is_array($translations)) {
                foreach ($translations as $value) {
                    $parts[] = $this->plain($value, 800);
                }
            }
        }

        if ($service->category) {
            $categoryTitles = $service->category->getTranslations('title');
            if (is_array($categoryTitles)) {
                foreach ($categoryTitles as $value) {
                    $parts[] = $this->plain($value, 200);
                }
            }
        }

        return $this->normalize(implode(' ', $parts));
    }

    private function translated(Service $service, string $field, string $locale, int $limit = 400): string
    {
        $value = $service->getTranslation($field, $locale);
        if (! is_string($value) || trim(strip_tags($value)) === '') {
            $value = $service->getTranslation($field, 'en');
        }

        return $this->plain($value, $limit);
    }

    private function normalize(?string $value): string
    {
        return strtolower(trim((string) $value));
    }

    private function plain(mixed $value, int $limit = 400): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $value)) ?? '');
        if ($text === '') {
            return '';
        }

        return mb_strlen($text) > $limit ? mb_substr($text, 0, $limit - 1).'…' : $text;
    }
}

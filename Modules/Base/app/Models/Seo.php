<?php

namespace Modules\Base\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

class Seo extends Model
{
    use HasTranslations;

    public $translatable = ['value'];

    public $timestamps = false;

    protected $table = 'seo';

    protected $fillable = ['key', 'value'];

    /**
     * Retrieve SEO value by key.
     *
     * @param  mixed|null  $default
     */
    public static function get(string $key, $default = null): mixed
    {
        $seo = self::getAllSeoEntries();

        $model = $seo->firstWhere('key', $key);

        return $model ? $model->value : $default ?? false;
    }

    /**
     * Get all SEO entries, with caching.
     */
    protected static function getAllSeoEntries(): Collection
    {
        $entries = Cache::remember('seo_entries', now()->addMinutes(10), function () {
            return self::query()->get()->map->getAttributes()->all();
        });

        return self::hydrate($entries);
    }

    /**
     * Set SEO value by key.
     */
    public static function set(string $key, string $value, ?bool $autoTranslate = null): bool
    {
        $seo = self::getAllSeoEntries();
        $model = $seo->firstWhere('key', $key);
        $shouldTranslate = $autoTranslate ?? wantsAutoTranslate();

        $translations = buildFieldTranslations(
            $value,
            $shouldTranslate,
            $model?->getTranslations('value')
        );

        if ($model) {
            $model->update(['value' => $translations]);
        } else {
            $model = self::create(['key' => $key, 'value' => $translations]);
            $seo->push($model);
        }

        self::cacheSeoEntries($seo);

        return true;
    }

    /**
     * Cache the SEO entries.
     */
    protected static function cacheSeoEntries(Collection $seo): void
    {
        Cache::put(
            'seo_entries',
            $seo->map->getAttributes()->all(),
            now()->addMinutes(10),
        );
    }
}

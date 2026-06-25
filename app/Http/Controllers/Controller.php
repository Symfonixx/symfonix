<?php

namespace App\Http\Controllers;

use Cache;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Modules\Base\Models\Country;
use Modules\Base\Support\Meta;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    final protected function setActive(string $key)
    {
        $active[$key] = true;
        view()->share('active', $active);

        return $this;
    }

    final protected function withCountries()
    {
        $countries = Country::hydrate(Cache::rememberForever('countries', static function () {
            return Country::query()->get()->map->getAttributes()->all();
        }));
        view()->share('countries', $countries);

        return $this;
    }

    /**
     * Convenience helper to render an Inertia page with optional custom meta data.
     *
     * Usage:
     *   return $this->inertia('Cms::BlogShow', [
     *       'blog' => $blog,
     *   ], (new Meta())->title($blog->title)->description($blog->description)->ogImage($blog->image_link)->toArray());
     */
    protected function inertia(string $component, array $props = [], ?array $meta = null)
    {
        if ($meta !== null) {
            $props['meta'] = $meta;
        }

        return Inertia::render($component, $props);
    }
}

<?php

namespace App\Http\Middleware;

use App;
use Cache;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Module;
use Modules\Base\Models\Seo;
use Modules\Base\Models\Settings;
use Modules\Cms\Models\Page;
use Modules\Services\Models\Service;

class HandleInertiaRequests extends Middleware {
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array {
        $shared = parent::share($request);

        // Helper function to safely get a value with fallback
        $safe = function ($callback, $fallback = null) {
            try {
                return $callback();
            } catch (\Throwable $e) {
                return $fallback;
            }
        };

        // Try to get shared data, but handle errors gracefully for error pages
        $shared = array_merge($shared, [
            'appName' => $safe(fn() => config('app.name'), 'Sham Vision'),
            'csrf' => $safe(fn() => csrf_token(), ''),
            'asset_path' => asset('/'),
            'storage_path' => asset('storage').'/',
            'locale' => $safe(fn() => App::currentLocale() ?: 'en', 'en'),
            'app_env' => $safe(fn() => config('app.env'), 'production'),
            'app_debug' => $safe(fn() => config('app.debug'), false),
            'translations' => $safe(fn() => $this->getTranslations(), []),
            'settings' => $safe(fn() => Settings::pluck('value', 'key'), []),
            'seo' => $safe(fn() => Seo::pluck('value', 'key'), ['website_name' => config('app.name', 'Sham Vision')]),
            'meta' => $safe(function () {
                $seo = Seo::pluck('value', 'key');
                $settings = Settings::pluck('value', 'key');

                $title = $seo->get('website_name');
                $description = $seo->get('website_desc');
                $metaImg = $settings->get('meta_img');

                return [
                    'title' => $title,
                    'description' => $description,
                    'robots' => 'index, follow',
                    'canonical' => url()->current(),
                    'og' => [
                        'title' => $title,
                        'description' => $description,
                        'image' => $metaImg,
                    ],
                    'twitter' => [
                        'title' => $title,
                        'description' => $description,
                        'image' => $metaImg,
                    ],
                ];
            }, []),
            'servicesList' => $safe(function () {
                return Cache::rememberForever('services', function () {
                    return Service::featured()->get();
                });
            }, []),
            'headerPages' => $safe(function () {
                return Cache::rememberForever('header_pages', function () {
                    return Page::published()
                        ->where('add_to_nav', true)
                        ->orderBy('created_at', 'desc')
                        ->get();
                });
            }, []),
            'footerPages' => $safe(function () {
                return Cache::rememberForever('footer_pages', function () {
                    return Page::published()
                        ->where('add_to_footer', true)
                        ->orderBy('created_at', 'desc')
                        ->get();
                });
            }, []),
            'auth' => fn() => $request->user()
                ? $request->user()->only('id', 'name', 'email', 'type')
                : null,
        ]);

        return $shared;
    }

    public function getTranslations(): array {

        $modules = Module::all();

        $locale = app()->getLocale();
        $translations = [];

        if ($locale === 'en') {
            return $translations;
        }

        // Iterate through each module to process the language file
        foreach ($modules as $module) {
            $modulePath = $module->getPath(); // Path to the module
            $langFilePath = $modulePath."/lang/$locale.json";

            if (file_exists($langFilePath)) {
                // Decode the JSON file and merge with translations
                $fileContent = json_decode(file_get_contents($langFilePath), true);

                if (is_array($fileContent)) {
                    $translations = array_merge($translations, $fileContent);
                }
            }
        }

        return $translations;
    }
}

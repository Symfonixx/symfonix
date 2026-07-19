<?php

use App\Http\Controllers\BotManController;
use App\Http\Controllers\DocsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

Route::match(['get', 'post'], '/botman', [BotManController::class, 'handle'])->name('botman.handle');

// Optional page that can host the widget iframe (used by frameEndpoint if desired).
Route::get('/chatbot', [BotManController::class, 'widget'])->name('chatbot.widget');

if (config('docs.enabled')) {
    Route::get('/docs/{path?}', DocsController::class)
        ->where('path', '.*')
        ->name('docs');
}

Route::get('/robots.txt', function () {
    $host = rtrim(request()->getSchemeAndHttpHost(), '/');
    $sitemapUrl = $host.'/sitemap.xml';

    $lines = [
        'User-agent: *',
        // Allow public content, but keep private/admin areas out of the index
        // so crawl budget is spent on pages that should rank.
        'Disallow: /admin',
        'Disallow: /login',
        'Disallow: /register',
        'Disallow: /password',
        'Disallow: /email/verify',
        'Disallow: /dashboard',
        'Disallow: /telescope',
        'Disallow: /pulse',
        'Disallow: /storage/framework',
        'Disallow: /*?*replytocom=',
        'Allow: /',
        '',
        'Sitemap: '.$sitemapUrl,
        '',
    ];

    return response(
        implode("\n", $lines),
        200,
        ['Content-Type' => 'text/plain; charset=UTF-8']
    );
});

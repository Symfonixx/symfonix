<?php

use App\Http\Controllers\BotManController;
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

Route::get('/robots.txt', function () {
    $sitemapUrl = rtrim(config('app.url'), '/') . '/sitemap.xml';

    return response(
        "User-agent: *\nDisallow:\nSitemap: {$sitemapUrl}\n",
        200,
        ['Content-Type' => 'text/plain; charset=UTF-8']
    );
});

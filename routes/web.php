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

// Legacy bare /admin URL (Ziggy excludes admin.* so old links used this) → locale dashboard.
Route::redirect('/admin', '/en/admin/dashboard');

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
    $llmsUrl = $host.'/llms.txt';

    $privateDisallows = [
        // Locale-prefixed private areas (e.g. /en/admin, /ar/portal).
        'Disallow: /*/admin',
        'Disallow: /*/portal',
        'Disallow: /*/login',
        'Disallow: /*/register',
        'Disallow: /*/password',
        'Disallow: /*/email/verify',
        'Disallow: /*/dashboard',
        'Disallow: /*/two-factor-challenge',
        // Non-localized / tooling paths.
        'Disallow: /admin',
        'Disallow: /portal',
        'Disallow: /login',
        'Disallow: /register',
        'Disallow: /password',
        'Disallow: /email/verify',
        'Disallow: /dashboard',
        'Disallow: /chatbot',
        'Disallow: /docs',
        'Disallow: /telescope',
        'Disallow: /pulse',
        'Disallow: /storage/framework',
        'Disallow: /*?*replytocom=',
        'Allow: /',
    ];

    // Explicitly allow major AI / LLM crawlers (GEO readiness).
    $aiAgents = [
        'GPTBot',
        'ChatGPT-User',
        'OAI-SearchBot',
        'ClaudeBot',
        'anthropic-ai',
        'PerplexityBot',
        'Google-Extended',
        'GoogleOther',
        'Amazonbot',
        'Bytespider',
        'CCBot',
        'meta-externalagent',
        'Applebot-Extended',
    ];

    $lines = [
        'User-agent: *',
        ...$privateDisallows,
        '',
    ];

    foreach ($aiAgents as $agent) {
        $lines[] = 'User-agent: '.$agent;
        $lines[] = 'Allow: /';
        $lines[] = '';
    }

    $lines[] = 'Sitemap: '.$sitemapUrl;
    $lines[] = '# LLM context: '.$llmsUrl;
    $lines[] = '';

    return response(
        implode("\n", $lines),
        200,
        ['Content-Type' => 'text/plain; charset=UTF-8']
    );
});

Route::get('/.well-known/mta-sts.txt', function () {
    $host = strtolower(request()->getHost());

    if (! str_starts_with($host, 'mta-sts.')) {
        abort(404);
    }

    $mode = config('mta-sts.mode', 'enforce');
    $maxAge = (int) config('mta-sts.max_age', 86400);
    $mxHosts = config('mta-sts.mx', ['mail.symfonix.io']);

    if ($mode !== 'enforce' && $mode !== 'testing') {
        $mode = 'enforce';
    }

    $lines = [
        'version: STSv1',
        'mode: '.$mode,
    ];

    foreach ($mxHosts as $mx) {
        if ($mx !== '') {
            $lines[] = 'mx: '.$mx;
        }
    }

    $lines[] = 'max_age: '.$maxAge;

    return response(
        implode("\n", $lines)."\n",
        200,
        [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age='.$maxAge,
        ]
    );
})->name('mta-sts.txt');

Route::get('/.well-known/security.txt', function () {
    $host = rtrim(request()->getSchemeAndHttpHost(), '/');
    $email = \Modules\Base\Models\Settings::get('email') ?: 'hello@symfonix.io';
    $expires = now()->addYear()->toIso8601String();

    $lines = [
        'Contact: mailto:'.$email,
        'Expires: '.$expires,
        'Canonical: '.$host.'/.well-known/security.txt',
        'Preferred-Languages: en, ar, tr, de',
        'Policy: '.$host.'/llms.txt',
    ];

    return response(
        implode("\n", $lines)."\n",
        200,
        [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400',
        ]
    );
})->name('security.txt');

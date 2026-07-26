<?php

use App\Http\Middleware\ContentSecurityPolicy;
use App\Http\Middleware\ForceCanonicalHost;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\TrackAdminEvents;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
            'is_customer' => \App\Http\Middleware\IsCustomer::class,
            /**** localS ****/
            'localize' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
        ]);

        $middleware->web(prepend: [
            ForceCanonicalHost::class,
        ]);

        $middleware->web(append: [
            ContentSecurityPolicy::class,
            HandleInertiaRequests::class,
            TrackAdminEvents::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->respond(function (Response $response, \Throwable $exception, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return $response;
            }

            $status = $response->getStatusCode();
            $pages = [
                400 => 'Error400',
                401 => 'Error401',
                403 => 'Error403',
                404 => 'Error404',
                500 => 'Error500',
                503 => 'Error500',
            ];

            if (! isset($pages[$status])) {
                return $response;
            }

            // Keep Laravel's detailed exception page for server errors in local/testing.
            if (in_array($status, [500, 503], true) && app()->environment(['local', 'testing'])) {
                return $response;
            }

            // Unmatched routes never hit the web middleware stack, so shared Inertia
            // props (settings, translations, asset_path, etc.) must be registered here.
            $inertia = app(HandleInertiaRequests::class);
            Inertia::setRootView($inertia->rootView($request));
            Inertia::version(fn () => $inertia->version($request));
            Inertia::share($inertia->share($request));

            return Inertia::render($pages[$status], [
                'status' => $status,
            ])
                ->toResponse($request)
                ->setStatusCode($status);
        });
    })

    ->create();

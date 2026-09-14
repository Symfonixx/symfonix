<?php

use App\Http\Middleware\ContentSecurityPolicy;
use App\Http\Middleware\ForceCanonicalHost;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsCustomer;
use App\Http\Middleware\TrackAdminEvents;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Inertia\Inertia;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Modules\User\Http\Middleware\EnsureCatalogPermission;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'is_admin' => IsAdmin::class,
            'is_customer' => IsCustomer::class,
            'catalog.permission' => EnsureCatalogPermission::class,
            /**** localS ****/
            'localize' => LaravelLocalizationRoutes::class,
            'localizationRedirect' => LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => LocaleSessionRedirect::class,
            'localeCookieRedirect' => LocaleCookieRedirect::class,
            'localeViewPath' => LaravelLocalizationViewPath::class,
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

        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
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

            if ($status === 403
                && ! $request->expectsJson()
                && ! $request->is('api/*')
                && $request->user()?->isAdmin()
            ) {
                return redirect()
                    ->route('admin.dashboard.index')
                    ->with('error', __('user::permissions.ui.forbidden_redirect'));
            }

            if (! isset($pages[$status])) {
                return $response;
            }

            // Keep Laravel's detailed exception page for server errors in local/testing.
            if (in_array($status, [500, 503], true) && app()->environment(['local', 'testing'])) {
                return $response;
            }

            // Unmatched routes never hit the web middleware stack, so hydrate the
            // session/cookies here before sharing Inertia props (auth, settings, etc.).
            if (! $request->hasSession()) {
                $pipeline = array_reduce(
                    array_reverse([
                        EncryptCookies::class,
                        AddQueuedCookiesToResponse::class,
                        StartSession::class,
                        ShareErrorsFromSession::class,
                    ]),
                    fn ($next, $middleware) => fn ($req) => app($middleware)->handle($req, $next),
                    fn ($req) => response('')
                );

                $pipeline($request);
            }

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

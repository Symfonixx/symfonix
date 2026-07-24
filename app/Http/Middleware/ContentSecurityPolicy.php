<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Build CSP directives
        $directives = $this->buildDirectives($request);

        // Set Content-Security-Policy header
        $response->headers->set('Content-Security-Policy', $directives);

        // SEO / security hygiene headers for public responses
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        if (config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    /**
     * Build the Content Security Policy directives.
     */
    private function buildDirectives(Request $request): string
    {
        $directives = [];

        // Default source - same origin
        $defaultSrc = ["'self'"];
        $directives['default-src'] = implode(' ', $defaultSrc);

        // Script sources - REQUIRED for XSS protection
        $scriptSrc = ["'self'"];

        // Allow Vite dev server in development
        if (config('app.debug') && config('app.env') !== 'production') {
            $scriptSrc[] = "'unsafe-eval'"; // Required for Vite HMR
            $scriptSrc[] = $request->getSchemeAndHttpHost() . ':5173'; // Vite dev server
        }

        // Allow CDN sources for admin/vendor views if needed
        // Note: For better security, consider moving to self-hosted assets
        if ($this->requiresCdnSources($request)) {
            $scriptSrc[] = 'https://cdnjs.cloudflare.com';
            $scriptSrc[] = 'https://cdn.jsdelivr.net';
            $scriptSrc[] = 'https://code.jquery.com';
            $scriptSrc[] = 'https://cdn.tiny.cloud';
        }

        // Allow inline scripts (consider using nonces in the future for better security)
        // Using 'unsafe-inline' is a security trade-off but necessary for current inline scripts
        $scriptSrc[] = "'unsafe-inline'";

        // Allow data URIs for inline scripts if needed
        $scriptSrc[] = 'data:';

        $directives['script-src'] = implode(' ', $scriptSrc);

        // Object sources - REQUIRED: Set to 'none' to prevent plugin injection
        $directives['object-src'] = "'none'";

        // Style sources
        $styleSrc = ["'self'", "'unsafe-inline'"];
        $styleSrc[] = 'https://fonts.googleapis.com';

        // Allow CDN sources for admin/vendor views if needed
        if ($this->requiresCdnSources($request)) {
            $styleSrc[] = 'https://cdnjs.cloudflare.com';
            $styleSrc[] = 'https://cdn.jsdelivr.net';
            $styleSrc[] = 'https://maxcdn.bootstrapcdn.com';
            $styleSrc[] = 'https://cdn.tiny.cloud';
        }

        $directives['style-src'] = implode(' ', $styleSrc);

        // Font sources
        $fontSrc = ["'self'", 'data:'];
        $fontSrc[] = 'https://fonts.gstatic.com';

        if ($this->requiresCdnSources($request)) {
            $fontSrc[] = 'https://cdnjs.cloudflare.com';
            $fontSrc[] = 'https://cdn.jsdelivr.net';
            $fontSrc[] = 'https://maxcdn.bootstrapcdn.com';
            $fontSrc[] = 'https://cdn.tiny.cloud';
        }

        $directives['font-src'] = implode(' ', $fontSrc);

        // Image sources
        $imgSrc = ["'self'", 'data:', 'blob:'];
        if ($this->requiresCdnSources($request)) {
            $imgSrc[] = 'https://cdn.tiny.cloud';
        }
        $directives['img-src'] = implode(' ', $imgSrc);

        // Connect sources (for AJAX, WebSocket, etc.)
        $connectSrc = ["'self'"];
        if (config('app.debug') && config('app.env') !== 'production') {
            $connectSrc[] = $request->getSchemeAndHttpHost() . ':5173'; // Vite dev server
        }
        if ($this->requiresCdnSources($request)) {
            $connectSrc[] = 'https://cdn.tiny.cloud';
        }
        $directives['connect-src'] = implode(' ', $connectSrc);

        // Frame sources (for iframes)
        $directives['frame-src'] = "'self'";

        // Base URI
        $directives['base-uri'] = "'self'";

        // Form action
        $directives['form-action'] = "'self'";

        // Upgrade insecure requests in production
        if (config('app.env') === 'production') {
            $directives['upgrade-insecure-requests'] = '';
        }

        // Build the final CSP string
        $cspString = [];
        foreach ($directives as $directive => $value) {
            if ($value === '') {
                $cspString[] = $directive;
            } else {
                $cspString[] = $directive . ' ' . $value;
            }
        }

        return implode('; ', $cspString);
    }

    /**
     * Admin routes are prefixed with a locale segment (e.g. /en/admin/logs).
     */
    private function requiresCdnSources(Request $request): bool
    {
        if ($request->routeIs('admin.*')) {
            return true;
        }

        return $request->is('admin/*', '*/admin/*', 'vendor/*', '*/vendor/*');
    }
}

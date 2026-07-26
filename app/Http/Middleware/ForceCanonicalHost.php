<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceCanonicalHost
{
    /**
     * Redirect www → apex (and optionally enforce HTTPS) so crawlers see one host.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->environment('production')) {
            return $next($request);
        }

        $host = strtolower($request->getHost());
        $targetHost = $host;

        if (str_starts_with($host, 'www.')) {
            $targetHost = substr($host, 4);
        }

        $wantsHttps = ! $request->isSecure()
            && strtolower((string) $request->header('X-Forwarded-Proto', '')) !== 'https';

        if ($targetHost !== $host || $wantsHttps) {
            $uri = $request->getRequestUri();
            $url = 'https://'.$targetHost.$uri;

            return redirect()->to($url, 301);
        }

        return $next($request);
    }
}

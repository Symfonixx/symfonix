<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\User\app\Support\AdminEventTracker;
use Symfony\Component\HttpFoundation\Response;

class TrackAdminEvents
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            AdminEventTracker::trackFromRequest($request, $response);
        } catch (\Throwable) {
            //
        }

        return $response;
    }
}

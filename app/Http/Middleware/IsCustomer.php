<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_if(! auth()->check() || auth()->user()->type !== User::TYPE_CUSTOMER, 403);

        return $next($request);
    }
}

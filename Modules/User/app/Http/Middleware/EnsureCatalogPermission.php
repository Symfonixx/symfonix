<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\User\Support\PermissionCatalog;
use Symfony\Component\HttpFoundation\Response;

class EnsureCatalogPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $routeName = $request->route()?->getName();

        if ($user === null || $routeName === null) {
            return $next($request);
        }

        if (PermissionCatalog::isExemptRoute($routeName)) {
            return $next($request);
        }

        if (PermissionCatalog::isFileManagerRequest($request)) {
            abort_unless($user->can('cms.file_manager.view'), 403);

            return $next($request);
        }

        $permission = PermissionCatalog::permissionForRoute($routeName, $request);

        if ($permission === null) {
            return $next($request);
        }

        $allowed = is_array($permission)
            ? $user->canany($permission)
            : $user->can($permission);

        abort_unless($allowed, 403);

        return $next($request);
    }
}

<?php

namespace Tests\Feature\SystemSettings;

use Illuminate\Support\Facades\Route;
use Modules\User\Support\PermissionCatalog;
use Tests\TestCase;

class AdminRouteRegistryCoverageTest extends TestCase
{
    public function test_all_permission_catalog_routes_are_registered(): void
    {
        $allowedMissing = [
            'admin.roles.create',
            'admin.roles.edit',
        ];

        $missing = [];

        foreach (PermissionCatalog::groups() as $group) {
            foreach ($group['tabs'] as $tab) {
                foreach ($tab['routes'] as $action => $routes) {
                    foreach ($routes as $routeName) {
                        if (in_array($routeName, $allowedMissing, true)) {
                            continue;
                        }

                        if (! Route::has($routeName)) {
                            $missing[] = $tab['key'].'.'.$action.' -> '.$routeName;
                        }
                    }
                }
            }
        }

        $this->assertSame([], $missing, 'Missing catalog routes: '.implode(', ', $missing));
    }
}

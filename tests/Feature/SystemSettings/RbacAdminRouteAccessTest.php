<?php

namespace Tests\Feature\SystemSettings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Support\PermissionCatalog;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\InteractsWithAdminPermissions;
use Tests\TestCase;

class RbacAdminRouteAccessTest extends TestCase
{
    use InteractsWithAdminPermissions;
    use RefreshDatabase;

    #[DataProvider('protectedRoutes')]
    public function test_user_without_permission_receives_403(string $method, string $routeName, array $routeParams = []): void
    {
        $user = $this->createAdminWithPermissions();

        $response = $this->callRoute($user, $method, $routeName, $routeParams);

        $this->assertContains($response->getStatusCode(), [403, 404]);
    }

    #[DataProvider('protectedRoutes')]
    public function test_user_with_permission_can_access_route(string $method, string $routeName, array $routeParams = []): void
    {
        $permission = PermissionCatalog::permissionForRoute($routeName);
        $permissions = is_array($permission) ? $permission : [$permission];

        $user = $this->createAdminWithPermissions(array_filter($permissions));
        $response = $this->callRoute($user, $method, $routeName, $routeParams);

        $this->assertNotSame(403, $response->getStatusCode());
        $this->assertLessThan(500, $response->getStatusCode());
    }

    /**
     * @return list<array{string, string, array<string, mixed>}>
     */
    public static function protectedRoutes(): array
    {
        return [
            ['GET', 'admin.crm.sales-forecasts.index', []],
            ['GET', 'admin.crm.marketing.index', ['channel' => 'whatsapp']],
            ['GET', 'admin.finance.dashboard', []],
            ['GET', 'admin.projects.index', []],
            ['GET', 'admin.fingerprint.index', []],
            ['GET', 'admin.system-configurations.index', []],
        ];
    }

    /**
     * @param  array<string, mixed>  $routeParams
     */
    private function callRoute(User $user, string $method, string $routeName, array $routeParams)
    {
        $url = route($routeName, $routeParams);

        return $this->actingAs($user)
            ->followingRedirects()
            ->call($method, $url);
    }
}

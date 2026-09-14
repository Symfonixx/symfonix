<?php

namespace Tests\Concerns;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

trait InteractsWithAdminPermissions
{
    /**
     * @param  list<string>  $permissionNames
     */
    protected function createAdminWithPermissions(array $permissionNames = []): User
    {
        $user = User::factory()->admin()->create();

        $this->grantPermissions($user, $permissionNames);

        return $user;
    }

    /**
     * @param  list<string>  $permissionNames
     */
    protected function grantPermissions(User $user, array $permissionNames): void
    {
        foreach ($permissionNames as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        $user->givePermissionTo($permissionNames);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Support\PermissionCatalog;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleScenarioSeeder extends Seeder
{
    /**
     * Seed practical back-office roles for the HR section.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [
            'HR Manager' => PermissionCatalog::keysForLegacyRole(['Hr Management']),
            'Sales Manager' => PermissionCatalog::keysForLegacyRole([
                'CRM Management',
                'CRM View All',
                'Sales Management',
                'Reporting Management',
            ]),
            'Finance Manager' => PermissionCatalog::keysForLegacyRole([
                'Finance Management',
                'Tax Management',
                'Reporting Management',
            ]),
            'Project Manager' => PermissionCatalog::keysForLegacyRole(['Project Management']),
            'Operations Manager' => PermissionCatalog::keysForLegacyRole([
                'Hr Management',
                'CRM Management',
                'Project Management',
                'Finance Management',
                'Tax Management',
                'Reporting Management',
            ]),
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::query()->firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $permissionModels = collect($permissions)
                ->map(fn (string $permission) => Permission::query()->firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web',
                ]));

            $role->syncPermissions($permissionModels);
        }
    }
}

<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
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
            'HR Manager' => [
                'Hr Management',
            ],
            'Sales Manager' => [
                'CRM Management',
                'CRM View All',
                'Sales Management',
            ],
            'Finance Manager' => [
                'Finance Management',
            ],
            'Project Manager' => [
                'Project Management',
            ],
            'Operations Manager' => [
                'Hr Management',
                'CRM Management',
                'Project Management',
                'Finance Management',
            ],
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

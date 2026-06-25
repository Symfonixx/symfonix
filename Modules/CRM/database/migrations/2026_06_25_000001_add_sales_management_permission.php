<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::query()->firstOrCreate([
            'name' => 'Sales Management',
            'guard_name' => 'web',
        ]);

        Role::query()->where('name', 'Admin')->each(
            fn (Role $role) => $role->givePermissionTo($permission)
        );

        $hrPermission = Permission::query()
            ->where('name', 'Hr Management')
            ->where('guard_name', 'web')
            ->first();

        if ($hrPermission) {
            Role::query()
                ->whereHas('permissions', fn ($query) => $query->where('permissions.id', $hrPermission->id))
                ->each(fn (Role $role) => $role->givePermissionTo($permission));
        }
    }

    public function down(): void
    {
        Permission::query()
            ->where('name', 'Sales Management')
            ->where('guard_name', 'web')
            ->delete();
    }
};

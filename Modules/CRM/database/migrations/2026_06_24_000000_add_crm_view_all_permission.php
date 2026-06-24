<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::query()->firstOrCreate([
            'name' => 'CRM View All',
            'guard_name' => 'web',
        ]);

        Role::query()->where('name', 'Admin')->each(
            fn (Role $role) => $role->givePermissionTo($permission)
        );
    }

    public function down(): void
    {
        Permission::query()
            ->where('name', 'CRM View All')
            ->where('guard_name', 'web')
            ->delete();
    }
};

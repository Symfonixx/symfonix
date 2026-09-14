<?php

namespace Modules\User\Services;

use Modules\User\Support\PermissionCatalog;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSync
{
    public function sync(bool $pruneUnknown = true): void
    {
        $keys = PermissionCatalog::allKeys();

        foreach ($keys as $key) {
            Permission::query()->firstOrCreate([
                'name' => $key,
                'guard_name' => 'web',
            ]);
        }

        $this->remapRoles($keys);

        if ($pruneUnknown) {
            Permission::query()
                ->where('guard_name', 'web')
                ->whereNotIn('name', $keys)
                ->delete();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @param  list<string>  $catalogKeys
     */
    private function remapRoles(array $catalogKeys): void
    {
        $roles = Role::query()->with('permissions')->get();

        foreach ($roles as $role) {
            if ($role->name === 'Admin') {
                $role->syncPermissions($catalogKeys);

                continue;
            }

            $current = $role->permissions->pluck('name')->all();
            $mapped = [];

            foreach ($current as $name) {
                if (in_array($name, $catalogKeys, true)) {
                    $mapped[] = $name;

                    continue;
                }

                $mapped = array_merge($mapped, PermissionCatalog::expandLegacy($name));
            }

            $mapped[] = 'overview.dashboard.view';
            $role->syncPermissions(array_values(array_unique($mapped)));
        }
    }
}

<?php

namespace Modules\User\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Modules\User\Services\PermissionSync;
use Modules\User\Support\PermissionCatalog;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SyncPermissionsCommand extends Command
{
    protected $signature = 'permissions:sync
                            {--no-prune : Keep permissions that are not in the catalog}
                            {--audit : List named admin routes that are not mapped in the catalog}';

    protected $description = 'Sync Spatie permissions with the granular catalog and remap existing roles.';

    public function handle(PermissionSync $sync): int
    {
        $prune = ! $this->option('no-prune');
        $sync->sync($prune);

        $this->components->info(sprintf(
            'Synced %d catalog permissions. Admin role has %d. Database has %d.',
            count(PermissionCatalog::allKeys()),
            Role::query()->where('name', 'Admin')->first()?->permissions()->count() ?? 0,
            Permission::query()->count(),
        ));

        if ($this->option('audit')) {
            $this->auditUnmappedAdminRoutes();
        }

        return self::SUCCESS;
    }

    private function auditUnmappedAdminRoutes(): void
    {
        $unmapped = [];

        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if (! is_string($name) || (! str_starts_with($name, 'admin.') && ! str_starts_with($name, 'api.reporting.'))) {
                continue;
            }

            if (PermissionCatalog::isExemptRoute($name)) {
                continue;
            }

            if (str_contains($name, 'unisharp.lfm') || str_contains($name, 'filemanager')) {
                continue;
            }

            if (PermissionCatalog::permissionForRoute($name) === null) {
                $unmapped[] = $name;
            }
        }

        $unmapped = array_values(array_unique($unmapped));
        sort($unmapped);

        if ($unmapped === []) {
            $this->components->info('All named admin/reporting API routes are mapped in the catalog.');

            return;
        }

        $this->components->warn(sprintf('%d unmapped routes (fail-open until catalogued):', count($unmapped)));
        foreach ($unmapped as $name) {
            $this->line('  - '.$name);
        }
    }
}

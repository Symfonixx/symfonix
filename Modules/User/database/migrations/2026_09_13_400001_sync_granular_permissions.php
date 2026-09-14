<?php

use Illuminate\Database\Migrations\Migration;
use Modules\User\Services\PermissionSync;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionSync::class)->sync();
    }

    public function down(): void
    {
        // Granular keys replace the legacy module-level names; rolling back
        // would drop live role assignments, so down() is intentionally empty.
    }
};

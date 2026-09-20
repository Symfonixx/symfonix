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
        // Permission keys are additive; rolling back would drop live role assignments.
    }
};

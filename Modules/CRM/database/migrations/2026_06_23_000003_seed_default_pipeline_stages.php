<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    public function up(): void
    {
        Artisan::call('db:seed', [
            '--class' => 'Modules\\CRM\\Database\\Seeders\\PipelineStageSeeder',
            '--force' => true,
        ]);
    }

    public function down(): void
    {
        // Stages may be referenced by deals; do not auto-delete.
    }
};

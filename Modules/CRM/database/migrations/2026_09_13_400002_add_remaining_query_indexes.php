<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('deals')) {
            Schema::table('deals', function (Blueprint $table) {
                $table->index(['status', 'assigned_to'], 'deals_status_assigned_to_index');
                $table->index(['status', 'pipeline_stage_id'], 'deals_status_pipeline_stage_id_index');
            });
        }

        if (Schema::hasTable('leads')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->index(['assigned_to', 'status'], 'leads_assigned_to_status_index');
                $table->index('blocked', 'leads_blocked_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('deals')) {
            Schema::table('deals', function (Blueprint $table) {
                $table->dropIndex('deals_status_assigned_to_index');
                $table->dropIndex('deals_status_pipeline_stage_id_index');
            });
        }

        if (Schema::hasTable('leads')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropIndex('leads_assigned_to_status_index');
                $table->dropIndex('leads_blocked_index');
            });
        }
    }
};

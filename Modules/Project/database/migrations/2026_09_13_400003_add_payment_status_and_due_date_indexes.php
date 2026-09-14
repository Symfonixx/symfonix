<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('projects')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->index('payment_status', 'projects_payment_status_index');
            $table->index('due_date', 'projects_due_date_index');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('projects')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('projects_payment_status_index');
            $table->dropIndex('projects_due_date_index');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('teams') || Schema::hasColumn('teams', 'employee_id')) {
            return;
        }

        Schema::table('teams', function (Blueprint $table) {
            $table->foreignId('employee_id')
                ->nullable()
                ->unique()
                ->constrained('employees')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('teams') || ! Schema::hasColumn('teams', 'employee_id')) {
            return;
        }

        Schema::table('teams', function (Blueprint $table) {
            $table->dropConstrainedForeignId('employee_id');
        });
    }
};

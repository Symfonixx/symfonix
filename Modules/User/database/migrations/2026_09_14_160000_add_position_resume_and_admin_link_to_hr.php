<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (! Schema::hasColumn('employees', 'position')) {
                    $table->string('position')->nullable()->after('mobile');
                }
                if (! Schema::hasColumn('employees', 'resume')) {
                    $table->string('resume')->nullable()->after('position');
                }
                if (! Schema::hasColumn('employees', 'user_id')) {
                    $table->foreignId('user_id')
                        ->nullable()
                        ->unique()
                        ->after('status')
                        ->constrained('users')
                        ->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('job_applications') && ! Schema::hasColumn('job_applications', 'employee_id')) {
            Schema::table('job_applications', function (Blueprint $table) {
                $table->foreignId('employee_id')
                    ->nullable()
                    ->after('job_position_id')
                    ->constrained('employees')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('job_applications') && Schema::hasColumn('job_applications', 'employee_id')) {
            Schema::table('job_applications', function (Blueprint $table) {
                $table->dropConstrainedForeignId('employee_id');
            });
        }

        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (Schema::hasColumn('employees', 'user_id')) {
                    $table->dropConstrainedForeignId('user_id');
                }
                if (Schema::hasColumn('employees', 'resume')) {
                    $table->dropColumn('resume');
                }
                if (Schema::hasColumn('employees', 'position')) {
                    $table->dropColumn('position');
                }
            });
        }
    }
};

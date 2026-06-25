<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('leads', 'assigned_to')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->foreignId('assigned_to')
                    ->nullable()
                    ->after('company_id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('leads', 'deal_id')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->foreignId('deal_id')
                    ->nullable()
                    ->after('assigned_to')
                    ->constrained('deals')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('leads', 'converted_at')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->timestamp('converted_at')->nullable()->after('deal_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'assigned_to')) {
                $table->dropForeign(['assigned_to']);
                $table->dropColumn('assigned_to');
            }

            if (Schema::hasColumn('leads', 'deal_id')) {
                $table->dropForeign(['deal_id']);
                $table->dropColumn('deal_id');
            }

            if (Schema::hasColumn('leads', 'converted_at')) {
                $table->dropColumn('converted_at');
            }
        });
    }
};

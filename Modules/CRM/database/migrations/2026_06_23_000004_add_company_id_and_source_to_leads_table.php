<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('leads', 'company_id')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->foreignId('company_id')
                    ->nullable()
                    ->after('company_name')
                    ->constrained('companies')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('leads', 'source')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->string('source', 50)
                    ->nullable()
                    ->default('website')
                    ->after('company_id')
                    ->index();
            });
        }
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }

            if (Schema::hasColumn('leads', 'source')) {
                $table->dropColumn('source');
            }
        });
    }
};

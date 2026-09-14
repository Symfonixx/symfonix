<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products') && ! Schema::hasColumn('products', 'tax_rate_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('tax_rate_id')
                    ->nullable()
                    ->after('currency')
                    ->constrained('tax_rates')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('projects') && ! Schema::hasColumn('projects', 'tax_rate_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->foreignId('tax_rate_id')
                    ->nullable()
                    ->after('currency')
                    ->constrained('tax_rates')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'tax_rate_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropConstrainedForeignId('tax_rate_id');
            });
        }

        if (Schema::hasTable('projects') && Schema::hasColumn('projects', 'tax_rate_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropConstrainedForeignId('tax_rate_id');
            });
        }
    }
};

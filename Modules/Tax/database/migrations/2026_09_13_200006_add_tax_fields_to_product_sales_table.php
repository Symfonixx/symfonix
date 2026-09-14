<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_sales')) {
            return;
        }

        Schema::table('product_sales', function (Blueprint $table) {
            if (! Schema::hasColumn('product_sales', 'tax_rate_id')) {
                $table->foreignId('tax_rate_id')
                    ->nullable()
                    ->after('unit_price')
                    ->constrained('tax_rates')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('product_sales', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('product_sales')) {
            return;
        }

        Schema::table('product_sales', function (Blueprint $table) {
            if (Schema::hasColumn('product_sales', 'tax_rate_id')) {
                $table->dropConstrainedForeignId('tax_rate_id');
            }

            if (Schema::hasColumn('product_sales', 'tax_amount')) {
                $table->dropColumn('tax_amount');
            }
        });
    }
};

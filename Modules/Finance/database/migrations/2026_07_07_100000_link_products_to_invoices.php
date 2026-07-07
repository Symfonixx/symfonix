<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->nullable()
                ->after('service_id')
                ->constrained('products')
                ->nullOnDelete();
        });

        Schema::table('product_sales', function (Blueprint $table) {
            $table->foreignId('invoice_id')
                ->nullable()
                ->after('company_id')
                ->constrained('invoices')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('product_sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('invoice_id');
        });

        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
        });
    }
};

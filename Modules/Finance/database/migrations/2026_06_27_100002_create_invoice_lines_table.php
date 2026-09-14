<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('invoice_lines')) {
            if (! Schema::hasColumn('invoice_lines', 'product_id')) {
                Schema::table('invoice_lines', function (Blueprint $table) {
                    $table->foreignId('product_id')
                        ->nullable()
                        ->after('service_id')
                        ->constrained('products')
                        ->nullOnDelete();
                });
            }

            Schema::table('invoice_lines', function (Blueprint $table) {
                if (! Schema::hasColumn('invoice_lines', 'tax_rate_id')) {
                    $table->unsignedBigInteger('tax_rate_id')->nullable()->after('unit_price');
                }
                if (! Schema::hasColumn('invoice_lines', 'tax_percent')) {
                    $table->decimal('tax_percent', 8, 4)->default(0)->after('tax_rate_id');
                }
                if (! Schema::hasColumn('invoice_lines', 'tax_amount')) {
                    $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_percent');
                }
            });

            return;
        }

        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('description');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 15, 2);
            $table->unsignedBigInteger('tax_rate_id')->nullable();
            $table->decimal('tax_percent', 8, 4)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('amount', 15, 2);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_lines');
    }
};

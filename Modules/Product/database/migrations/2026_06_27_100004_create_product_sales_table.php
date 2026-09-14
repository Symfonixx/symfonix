<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_sales')) {
            Schema::create('product_sales', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
                $table->foreignId('deal_id')->nullable()->constrained('deals')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->unsignedInteger('quantity')->default(1);
                $table->decimal('unit_price', 15, 2);
                $table->unsignedBigInteger('tax_rate_id')->nullable();
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2);
                $table->string('currency', 3)->default('USD');
                $table->text('notes')->nullable();
                $table->date('sold_at')->index();
                $table->timestamps();

                $table->unique(['deal_id', 'product_id']);
            });

            return;
        }

        // Existing installs created product_sales via the old 2026_06_25_100003
        // migration; only add the invoice link if it is still missing.
        if (! Schema::hasColumn('product_sales', 'invoice_id')) {
            Schema::table('product_sales', function (Blueprint $table) {
                $table->foreignId('invoice_id')
                    ->nullable()
                    ->after('company_id')
                    ->constrained('invoices')
                    ->nullOnDelete();
            });
        }

        Schema::table('product_sales', function (Blueprint $table) {
            if (! Schema::hasColumn('product_sales', 'tax_rate_id')) {
                $table->unsignedBigInteger('tax_rate_id')->nullable()->after('unit_price');
            }
            if (! Schema::hasColumn('product_sales', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate_id');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sales');
    }
};

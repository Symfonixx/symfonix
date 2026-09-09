<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('quotes')) {
            Schema::create('quotes', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->string('quote_number')->unique();
                $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
                $table->foreignId('deal_id')->constrained('deals')->restrictOnDelete();
                $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
                $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired', 'void'])
                    ->default('draft')
                    ->index();
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('total', 15, 2)->default(0);
                $table->string('currency', 3)->default('USD');
                $table->text('terms')->nullable();
                $table->text('notes')->nullable();
                $table->date('issued_at');
                $table->date('expires_at')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->string('responder_name')->nullable();
                $table->string('responder_email')->nullable();
                $table->string('responder_ip', 45)->nullable();
                $table->text('response_note')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['company_id', 'status']);
                $table->index('deal_id');
                $table->index('expires_at');
            });
        }

        if (! Schema::hasTable('quote_lines')) {
            Schema::create('quote_lines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quote_id')->constrained('quotes')->cascadeOnDelete();
                $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
                $table->enum('item_type', ['service', 'product']);
                $table->string('description');
                $table->unsignedInteger('quantity')->default(1);
                $table->decimal('unit_price', 15, 2);
                $table->decimal('discount_percent', 8, 2)->default(0);
                $table->decimal('tax_percent', 8, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('amount', 15, 2);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_lines');
        Schema::dropIfExists('quotes');
    }
};

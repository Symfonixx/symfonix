<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('invoices')) {
            if (! Schema::hasColumn('invoices', 'project_id')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->foreignId('project_id')
                        ->nullable()
                        ->after('deal_id')
                        ->constrained('projects')
                        ->nullOnDelete();
                    $table->index('project_id');
                });
            }

            return;
        }

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->nullOnDelete();
            $table->foreignId('deal_id')->nullable()->constrained('deals')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'void'])->default('draft')->index();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->date('issued_at');
            $table->date('due_at');
            $table->date('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index('due_at');
            $table->index('project_id');
            $table->index(['status', 'paid_at'], 'invoices_status_paid_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

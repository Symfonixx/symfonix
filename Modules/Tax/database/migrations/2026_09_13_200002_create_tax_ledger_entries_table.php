<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tax_ledger_entries')) {
            return;
        }

        Schema::create('tax_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_rate_id')->nullable()->constrained('tax_rates')->nullOnDelete();
            $table->string('direction', 10);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('base_amount', 15, 2)->nullable();
            $table->date('transaction_date');
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->nullOnDelete();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index(['direction', 'transaction_date']);
            $table->index(['source_type', 'source_id']);
            $table->index(['company_id', 'transaction_date']);
            $table->index(['project_id', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_ledger_entries');
    }
};

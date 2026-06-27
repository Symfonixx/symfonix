<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->enum('flow', ['revenue', 'expense']);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->nullableMorphs('reference');
            $table->text('description')->nullable();
            $table->date('transaction_date');
            $table->timestamps();

            $table->index(['flow', 'transaction_date']);
        });

        Schema::create('journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            $table->enum('side', ['debit', 'credit']);
            $table->enum('account', ['cash', 'revenue', 'expense']);
            $table->foreignId('expense_category_id')
                ->nullable()
                ->constrained('expense_categories')
                ->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            $table->index(['account', 'side']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_lines');
        Schema::dropIfExists('journal_entries');
    }
};

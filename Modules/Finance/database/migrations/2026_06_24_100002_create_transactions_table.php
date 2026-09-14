<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('journal_entries')) {
            if (! Schema::hasColumn('journal_entries', 'exchange_rate')) {
                Schema::table('journal_entries', function (Blueprint $table) {
                    $table->decimal('exchange_rate', 18, 8)
                        ->default(1)
                        ->after('currency')
                        ->comment('Units of system base currency per 1 unit of transaction currency at posting time');
                });
            }
            if (! Schema::hasColumn('journal_entries', 'base_amount')) {
                Schema::table('journal_entries', function (Blueprint $table) {
                    $table->decimal('base_amount', 15, 2)
                        ->nullable()
                        ->after('exchange_rate')
                        ->comment('Amount converted to system base currency at posting time');
                });
            }

            if (! Schema::hasColumn('journal_entries', 'tax_rate_id')) {
                Schema::table('journal_entries', function (Blueprint $table) {
                    $table->unsignedBigInteger('tax_rate_id')->nullable()->after('description');
                });
            }
            if (! Schema::hasColumn('journal_entries', 'tax_amount')) {
                Schema::table('journal_entries', function (Blueprint $table) {
                    $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate_id');
                });
            }

            if (! Schema::hasTable('journal_lines')) {
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

            return;
        }

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->enum('flow', ['revenue', 'expense']);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 18, 8)
                ->default(1)
                ->comment('Units of system base currency per 1 unit of transaction currency at posting time');
            $table->decimal('base_amount', 15, 2)
                ->nullable()
                ->comment('Amount converted to system base currency at posting time');
            $table->nullableMorphs('reference');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('tax_rate_id')->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0);
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transactions')) {
            return;
        }

        if (! Schema::hasTable('journal_entries')) {
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

        DB::table('transactions')->orderBy('id')->chunkById(100, function ($rows) {
            foreach ($rows as $row) {
                $flow = $row->type === 'income' ? 'revenue' : 'expense';
                $entryId = DB::table('journal_entries')->insertGetId([
                    'flow' => $flow,
                    'amount' => $row->amount,
                    'currency' => $row->currency ?? 'USD',
                    'reference_type' => $row->reference_type,
                    'reference_id' => $row->reference_id,
                    'description' => $row->description,
                    'transaction_date' => $row->transaction_date,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);

                if ($flow === 'revenue') {
                    DB::table('journal_lines')->insert([
                        [
                            'journal_entry_id' => $entryId,
                            'side' => 'debit',
                            'account' => 'cash',
                            'expense_category_id' => null,
                            'amount' => $row->amount,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ],
                        [
                            'journal_entry_id' => $entryId,
                            'side' => 'credit',
                            'account' => 'revenue',
                            'expense_category_id' => null,
                            'amount' => $row->amount,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ],
                    ]);
                } else {
                    DB::table('journal_lines')->insert([
                        [
                            'journal_entry_id' => $entryId,
                            'side' => 'debit',
                            'account' => 'expense',
                            'expense_category_id' => $row->expense_category_id,
                            'amount' => $row->amount,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ],
                        [
                            'journal_entry_id' => $entryId,
                            'side' => 'credit',
                            'account' => 'cash',
                            'expense_category_id' => null,
                            'amount' => $row->amount,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ],
                    ]);
                }
            }
        });

        Schema::dropIfExists('transactions');
    }

    public function down(): void
    {
        // Irreversible: legacy single-sided transactions are not restored.
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Legacy financial data is assumed to have been recorded in USD.
 * This migration normalizes null/empty currencies and snapshots rates at 1.0.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('journal_entries')) {
            DB::table('journal_entries')
                ->where(function ($query) {
                    $query->whereNull('currency')
                        ->orWhere('currency', '')
                        ->orWhereRaw('LENGTH(TRIM(currency)) = 0');
                })
                ->update(['currency' => 'USD']);

            DB::table('journal_entries')->update([
                'currency' => DB::raw("UPPER(TRIM(currency))"),
            ]);

            if (Schema::hasColumn('journal_entries', 'exchange_rate')) {
                DB::table('journal_entries')
                    ->whereNull('exchange_rate')
                    ->orWhere('exchange_rate', 0)
                    ->update(['exchange_rate' => 1]);
            }

            if (Schema::hasColumn('journal_entries', 'base_amount')) {
                DB::table('journal_entries')
                    ->whereNull('base_amount')
                    ->update([
                        'base_amount' => DB::raw('ROUND(amount * COALESCE(NULLIF(exchange_rate, 0), 1), 2)'),
                    ]);
            }
        }

        if (Schema::hasTable('projects') && Schema::hasColumn('projects', 'currency')) {
            DB::table('projects')
                ->where(function ($query) {
                    $query->whereNull('currency')
                        ->orWhere('currency', '');
                })
                ->update(['currency' => 'USD']);

            DB::table('projects')->update([
                'currency' => DB::raw('UPPER(TRIM(currency))'),
                'budget_exchange_rate' => DB::raw('COALESCE(NULLIF(budget_exchange_rate, 0), 1)'),
            ]);
        }

        $tablesWithCurrency = [
            'deals',
            'subscriptions',
            'products',
            'product_sales',
            'invoices',
        ];

        foreach ($tablesWithCurrency as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'currency')) {
                continue;
            }

            DB::table($table)
                ->where(function ($query) {
                    $query->whereNull('currency')
                        ->orWhere('currency', '');
                })
                ->update(['currency' => 'USD']);

            DB::table($table)->update([
                'currency' => DB::raw('UPPER(TRIM(currency))'),
            ]);
        }
    }

    public function down(): void
    {
        // Irreversible data normalization.
    }
};

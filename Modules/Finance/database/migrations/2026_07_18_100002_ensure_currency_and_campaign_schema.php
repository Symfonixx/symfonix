<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Bridge for existing deployed databases after currency / campaign alter
 * migrations were folded into create migrations. Safe no-op on fresh installs.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->ensureExchangeRates();
        $this->ensureJournalEntryCurrencyColumns();
        $this->ensureProjectCurrencyColumns();
        $this->ensureMarketingCampaignSchema();
        $this->backfillLegacyUsdCurrencyData();
    }

    public function down(): void
    {
        // Irreversible schema sync for existing installs.
    }

    private function ensureExchangeRates(): void
    {
        if (Schema::hasTable('exchange_rates')) {
            return;
        }

        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('base_currency', 3);
            $table->string('target_currency', 3);
            $table->decimal('rate', 18, 8);
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->unique(['base_currency', 'target_currency']);
            $table->index('fetched_at');
        });
    }

    private function ensureJournalEntryCurrencyColumns(): void
    {
        if (! Schema::hasTable('journal_entries')) {
            return;
        }

        Schema::table('journal_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('journal_entries', 'exchange_rate')) {
                $table->decimal('exchange_rate', 18, 8)
                    ->default(1)
                    ->after('currency')
                    ->comment('Units of system base currency per 1 unit of transaction currency at posting time');
            }
            if (! Schema::hasColumn('journal_entries', 'base_amount')) {
                $table->decimal('base_amount', 15, 2)
                    ->nullable()
                    ->after('exchange_rate')
                    ->comment('Amount converted to system base currency at posting time');
            }
        });
    }

    private function ensureProjectCurrencyColumns(): void
    {
        if (! Schema::hasTable('projects')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'currency')) {
                $table->string('currency', 3)->default('USD')->after('budget');
            }
            if (! Schema::hasColumn('projects', 'budget_exchange_rate')) {
                $table->decimal('budget_exchange_rate', 18, 8)
                    ->default(1)
                    ->after('currency')
                    ->comment('Units of system base currency per 1 unit of project currency when budget was set');
            }
        });
    }

    private function ensureMarketingCampaignSchema(): void
    {
        if (! Schema::hasTable('marketing_campaigns')) {
            return;
        }

        if (! Schema::hasColumn('marketing_campaigns', 'status')) {
            Schema::table('marketing_campaigns', function (Blueprint $table) {
                $table->string('status', 20)->default('pending')->after('recipients_count');
            });

            // Existing campaigns were already dispatched before status tracking existed.
            DB::table('marketing_campaigns')->update(['status' => 'finished']);
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE marketing_campaigns MODIFY subject TEXT NOT NULL');
        }
    }

    private function backfillLegacyUsdCurrencyData(): void
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
                'currency' => DB::raw('UPPER(TRIM(currency))'),
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

        foreach (['deals', 'subscriptions', 'products', 'product_sales', 'invoices'] as $table) {
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
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('invoice_lines')) {
            return;
        }

        Schema::table('invoice_lines', function (Blueprint $table) {
            if (! Schema::hasColumn('invoice_lines', 'tax_rate_id')) {
                $table->foreignId('tax_rate_id')
                    ->nullable()
                    ->after('unit_price')
                    ->constrained('tax_rates')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('invoice_lines', 'tax_percent')) {
                $table->decimal('tax_percent', 8, 4)->default(0)->after('tax_rate_id');
            }

            if (! Schema::hasColumn('invoice_lines', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_percent');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('invoice_lines')) {
            return;
        }

        Schema::table('invoice_lines', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_lines', 'tax_rate_id')) {
                $table->dropConstrainedForeignId('tax_rate_id');
            }

            if (Schema::hasColumn('invoice_lines', 'tax_percent')) {
                $table->dropColumn('tax_percent');
            }

            if (Schema::hasColumn('invoice_lines', 'tax_amount')) {
                $table->dropColumn('tax_amount');
            }
        });
    }
};

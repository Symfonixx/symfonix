<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->decimal('exchange_rate', 18, 8)
                ->default(1)
                ->after('currency')
                ->comment('Units of system base currency per 1 unit of transaction currency at posting time');
            $table->decimal('base_amount', 15, 2)
                ->nullable()
                ->after('exchange_rate')
                ->comment('Amount converted to system base currency at posting time');
        });
    }

    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropColumn(['exchange_rate', 'base_amount']);
        });
    }
};

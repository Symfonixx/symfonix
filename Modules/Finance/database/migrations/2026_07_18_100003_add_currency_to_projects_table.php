<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('currency', 3)->default('USD')->after('budget');
            $table->decimal('budget_exchange_rate', 18, 8)
                ->default(1)
                ->after('currency')
                ->comment('Units of system base currency per 1 unit of project currency when budget was set');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['currency', 'budget_exchange_rate']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('assigned_to')
                ->nullable()
                ->after('company_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('deal_id')
                ->nullable()
                ->after('assigned_to')
                ->constrained('deals')
                ->nullOnDelete();

            $table->timestamp('converted_at')->nullable()->after('deal_id');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['deal_id']);
            $table->dropColumn(['assigned_to', 'deal_id', 'converted_at']);
        });
    }
};

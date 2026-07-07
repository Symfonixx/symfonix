<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->date('period')->nullable()->after('base_salary');
        });

        DB::table('salaries')->orderBy('id')->each(function ($salary) {
            $period = $salary->paid_at
                ?? $salary->created_at
                ?? now()->toDateString();

            DB::table('salaries')
                ->where('id', $salary->id)
                ->update(['period' => date('Y-m-01', strtotime($period))]);
        });

        DB::statement('ALTER TABLE salaries MODIFY period DATE NOT NULL');

        Schema::table('salaries', function (Blueprint $table) {
            $table->unique(['employee_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropUnique(['employee_id', 'period']);
            $table->dropColumn('period');
        });
    }
};

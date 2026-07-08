<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('salaries')) {
            if (! Schema::hasColumn('salaries', 'period')) {
                Schema::table('salaries', function (Blueprint $table) {
                    $table->date('period')->nullable()->after('base_salary');
                });

                DB::table('salaries')->orderBy('id')->each(function ($salary) {
                    $period = $salary->paid_at
                        ?? $salary->created_at
                        ?? now()->toDateString();

                    DB::table('salaries')
                        ->where('id', $salary->id)
                        ->update(['period' => date('Y-m-01', strtotime((string) $period))]);
                });

                DB::statement('ALTER TABLE salaries MODIFY period DATE NOT NULL');

                Schema::table('salaries', function (Blueprint $table) {
                    $table->unique(['employee_id', 'period']);
                });
            }

            return;
        }

        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->decimal('base_salary', 15, 2);
            $table->date('period');
            $table->date('paid_at')->nullable();
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamps();

            $table->unique(['employee_id', 'period']);
            $table->index(['status', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};

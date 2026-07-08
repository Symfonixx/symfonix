<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('project_employees')) {
            Schema::create('project_employees', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->string('role')->nullable();
                $table->date('started_at');
                $table->date('ended_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['project_id', 'employee_id']);
                $table->index(['employee_id', 'ended_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_employees');
    }
};

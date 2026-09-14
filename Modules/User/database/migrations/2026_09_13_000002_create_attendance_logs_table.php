<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attendance_logs')) {
            return;
        }

        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('device_user_id', 32);
            $table->unsignedInteger('device_uid')->nullable();
            $table->unsignedTinyInteger('punch_state')->default(255);
            $table->unsignedTinyInteger('verify_mode')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'recorded_at', 'punch_state'], 'attendance_logs_employee_punch_unique');
            $table->index('recorded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_positions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('department')->index();
            $table->longText('description');
            $table->longText('requirements')->nullable();
            $table->enum('status', ['active', 'closed'])->default('active')->index();
            $table->date('posted_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_positions');
    }
};

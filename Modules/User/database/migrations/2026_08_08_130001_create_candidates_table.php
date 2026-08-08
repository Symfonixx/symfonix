<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->index();
            $table->string('phone', 50);
            $table->decimal('expected_salary', 15, 2)->nullable();
            $table->text('motivation')->nullable();
            $table->string('resume_path');
            $table->longText('cover_letter')->nullable();
            $table->text('profile_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};

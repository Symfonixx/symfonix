<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_use_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->json('title');
            $table->string('slug')->unique();
            $table->json('client_name')->nullable();
            $table->json('summary')->nullable();
            $table->json('challenge')->nullable();
            $table->json('solution')->nullable();
            $table->json('results')->nullable();
            $table->json('content')->nullable();
            $table->string('image')->nullable();
            $table->json('technologies')->nullable();
            $table->string('category_tag')->nullable();
            $table->string('project_url')->nullable();
            $table->unsignedSmallInteger('completed_year')->nullable();
            $table->boolean('featured')->default(false)->index();
            $table->string('status')->default('Archived')->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->unsignedInteger('visits')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_use_cases');
    }
};

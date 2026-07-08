<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Link testimonials to completed project reviews (project_id + customer_id),
     * and remove legacy CMS fields (avatar, name, position, url).
     */
    public function up(): void
    {
        if (! Schema::hasTable('testimonials')) {
            return;
        }

        if (Schema::hasColumn('testimonials', 'project_id')) {
            return;
        }

        Schema::dropIfExists('testimonials');

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->unique()->constrained('projects')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->json('quote');
            $table->enum('status', ['Published', 'Archived'])->default('Published');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('avatar');
            $table->json('name');
            $table->json('position')->nullable();
            $table->string('url')->nullable();
            $table->json('quote');
            $table->enum('status', ['Published', 'Archived'])->default('Published');
            $table->timestamps();
        });
    }
};

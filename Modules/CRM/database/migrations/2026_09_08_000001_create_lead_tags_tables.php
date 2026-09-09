<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lead_tags')) {
            Schema::create('lead_tags', function (Blueprint $table) {
                $table->id();
                $table->json('name');
                $table->string('color', 20)->default('primary');
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('lead_lead_tag')) {
            Schema::create('lead_lead_tag', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
                $table->foreignId('lead_tag_id')->constrained('lead_tags')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['lead_id', 'lead_tag_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_lead_tag');
        Schema::dropIfExists('lead_tags');
    }
};

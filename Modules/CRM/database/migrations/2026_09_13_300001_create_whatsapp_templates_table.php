<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('whatsapp_templates')) {
            return;
        }

        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('language', 10)->default('en');
            $table->string('category', 30)->default('MARKETING');
            $table->string('status', 20)->default('approved');
            $table->string('header_type', 20)->default('none');
            $table->text('header_content')->nullable();
            $table->text('body');
            $table->string('footer')->nullable();
            $table->json('buttons')->nullable();
            $table->string('meta_template_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['name', 'language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_templates');
    }
};

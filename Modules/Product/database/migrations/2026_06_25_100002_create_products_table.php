<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_category_id')->constrained('product_categories')->cascadeOnDelete();
            $table->json('name');
            $table->string('slug')->unique();
            $table->json('short_description')->nullable();
            $table->string('sku')->unique();
            $table->json('description')->nullable();
            $table->string('main_image')->nullable();
            $table->string('seo_meta_img')->nullable();
            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();
            $table->json('seo_keywords')->nullable();
            $table->decimal('price', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('billing_type', ['one_time', 'monthly', 'quarterly', 'yearly'])->default('one_time');
            $table->enum('status', ['active', 'archived'])->default('active')->index();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

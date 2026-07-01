<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('is_featured');
            $table->text('short_description')->nullable()->after('slug');
            $table->string('main_image')->nullable()->after('description');
            $table->string('seo_meta_img')->nullable()->after('main_image');
            $table->string('seo_title')->nullable()->after('seo_meta_img');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('seo_keywords')->nullable()->after('seo_description');
        });

        DB::statement('ALTER TABLE products MODIFY description LONGTEXT NULL');
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'is_published',
                'short_description',
                'main_image',
                'seo_meta_img',
                'seo_title',
                'seo_description',
                'seo_keywords',
            ]);
        });

        DB::statement('ALTER TABLE products MODIFY description TEXT NULL');
    }
};

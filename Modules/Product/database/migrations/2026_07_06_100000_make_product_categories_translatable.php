<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $defaultLocale = config('app.locale', 'en');

        foreach (DB::table('product_categories')->get() as $category) {
            DB::table('product_categories')->where('id', $category->id)->update([
                'name' => json_encode([$defaultLocale => $category->name], JSON_UNESCAPED_UNICODE),
                'description' => $category->description
                    ? json_encode([$defaultLocale => $category->description], JSON_UNESCAPED_UNICODE)
                    : null,
            ]);
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE product_categories MODIFY name JSON NOT NULL');
            DB::statement('ALTER TABLE product_categories MODIFY description JSON NULL');

            return;
        }

        Schema::table('product_categories', function (Blueprint $table) {
            $table->json('name_json');
            $table->json('description_json')->nullable();
        });

        foreach (DB::table('product_categories')->get() as $category) {
            DB::table('product_categories')->where('id', $category->id)->update([
                'name_json' => $category->name,
                'description_json' => $category->description,
            ]);
        }

        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->renameColumn('name_json', 'name');
            $table->renameColumn('description_json', 'description');
        });
    }

    public function down(): void
    {
        $defaultLocale = config('app.locale', 'en');

        foreach (DB::table('product_categories')->get() as $category) {
            $name = json_decode($category->name, true);
            $description = $category->description ? json_decode($category->description, true) : null;

            DB::table('product_categories')->where('id', $category->id)->update([
                'name' => $name[$defaultLocale] ?? (is_array($name) ? reset($name) : $category->name),
                'description' => $description
                    ? ($description[$defaultLocale] ?? reset($description))
                    : null,
            ]);
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE product_categories MODIFY name VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE product_categories MODIFY description TEXT NULL');
        }
    }
};

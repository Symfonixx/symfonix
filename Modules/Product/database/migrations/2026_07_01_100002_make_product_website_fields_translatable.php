<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $translatableFields = [
        'name',
        'short_description',
        'description',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    public function up(): void
    {
        $defaultLocale = 'en';

        if (Schema::hasTable('products')) {
            DB::table('products')->orderBy('id')->chunkById(100, function ($products) use ($defaultLocale) {
                foreach ($products as $product) {
                    $updates = [];

                    foreach ($this->translatableFields as $field) {
                        $value = $product->{$field};

                        if ($value === null || $value === '') {
                            continue;
                        }

                        if ($this->isJson($value)) {
                            continue;
                        }

                        $updates[$field] = json_encode([$defaultLocale => $value], JSON_UNESCAPED_UNICODE);
                    }

                    if ($updates !== []) {
                        DB::table('products')->where('id', $product->id)->update($updates);
                    }
                }
            });
        }

        DB::statement('ALTER TABLE products MODIFY name JSON NOT NULL');
        DB::statement('ALTER TABLE products MODIFY short_description JSON NULL');
        DB::statement('ALTER TABLE products MODIFY description JSON NULL');
        DB::statement('ALTER TABLE products MODIFY seo_title JSON NULL');
        DB::statement('ALTER TABLE products MODIFY seo_description JSON NULL');
        DB::statement('ALTER TABLE products MODIFY seo_keywords JSON NULL');
    }

    public function down(): void
    {
        $defaultLocale = 'en';

        if (Schema::hasTable('products')) {
            DB::table('products')->orderBy('id')->chunkById(100, function ($products) use ($defaultLocale) {
                foreach ($products as $product) {
                    $updates = [];

                    foreach ($this->translatableFields as $field) {
                        $value = $product->{$field};

                        if (! $this->isJson($value)) {
                            continue;
                        }

                        $decoded = json_decode($value, true);
                        $updates[$field] = is_array($decoded)
                            ? ($decoded[$defaultLocale] ?? reset($decoded) ?: null)
                            : $value;
                    }

                    if ($updates !== []) {
                        DB::table('products')->where('id', $product->id)->update($updates);
                    }
                }
            });
        }

        DB::statement('ALTER TABLE products MODIFY name VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE products MODIFY short_description TEXT NULL');
        DB::statement('ALTER TABLE products MODIFY description LONGTEXT NULL');
        DB::statement('ALTER TABLE products MODIFY seo_title VARCHAR(255) NULL');
        DB::statement('ALTER TABLE products MODIFY seo_description TEXT NULL');
        DB::statement('ALTER TABLE products MODIFY seo_keywords VARCHAR(255) NULL');
    }

    private function isJson(mixed $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        json_decode($value);

        return json_last_error() === JSON_ERROR_NONE;
    }
};

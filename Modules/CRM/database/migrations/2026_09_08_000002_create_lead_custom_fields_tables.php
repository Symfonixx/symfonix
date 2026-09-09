<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lead_custom_fields')) {
            Schema::create('lead_custom_fields', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->json('label');
                $table->string('type', 30)->default('text');
                $table->json('options')->nullable();
                $table->boolean('is_required')->default(false);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('leads') && ! Schema::hasColumn('leads', 'custom_fields')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->json('custom_fields')->nullable()->after('meta');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'custom_fields')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropColumn('custom_fields');
            });
        }

        Schema::dropIfExists('lead_custom_fields');
    }
};

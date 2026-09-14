<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tax_rates')) {
            return;
        }

        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('percentage', 8, 4);
            $table->string('type', 20);
            $table->string('region_code', 10)->nullable();
            $table->string('status', 20)->default('active');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['status', 'region_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};

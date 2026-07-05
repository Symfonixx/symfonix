<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The CRM Deal model relies on this pivot (deal <-> service). Its original
        // migration lives in the Product module and can be missing on some installs,
        // which causes a QueryException on the deal pages. Create it defensively.
        if (Schema::hasTable('deal_service')) {
            return;
        }

        Schema::create('deal_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained('deals')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['deal_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_service');
    }
};

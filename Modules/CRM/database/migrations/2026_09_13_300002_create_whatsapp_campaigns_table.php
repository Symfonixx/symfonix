<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('whatsapp_campaigns')) {
            return;
        }

        Schema::create('whatsapp_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('whatsapp_template_id')->constrained()->cascadeOnDelete();
            $table->json('template_parameters')->nullable();
            $table->text('rendered_preview')->nullable();
            $table->unsignedInteger('recipients_count')->default(0);
            $table->string('status', 20)->default('pending');
            $table->json('recipient_sources')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at'], 'whatsapp_campaigns_status_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_campaigns');
    }
};

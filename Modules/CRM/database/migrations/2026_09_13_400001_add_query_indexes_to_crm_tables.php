<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('whatsapp_campaigns')) {
            Schema::table('whatsapp_campaigns', function (Blueprint $table) {
                $table->index(['status', 'created_at'], 'whatsapp_campaigns_status_created_at_index');
            });
        }

        if (Schema::hasTable('deals')) {
            Schema::table('deals', function (Blueprint $table) {
                $table->index(['status', 'won_at'], 'deals_status_won_at_index');
                $table->index(['status', 'lost_at'], 'deals_status_lost_at_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('whatsapp_campaigns')) {
            Schema::table('whatsapp_campaigns', function (Blueprint $table) {
                $table->dropIndex('whatsapp_campaigns_status_created_at_index');
            });
        }

        if (Schema::hasTable('deals')) {
            Schema::table('deals', function (Blueprint $table) {
                $table->dropIndex('deals_status_won_at_index');
                $table->dropIndex('deals_status_lost_at_index');
            });
        }
    }
};

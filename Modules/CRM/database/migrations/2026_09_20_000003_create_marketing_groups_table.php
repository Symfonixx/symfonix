<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('marketing_groups')) {
            Schema::create('marketing_groups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->text('goal');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('marketing_campaigns') && ! Schema::hasColumn('marketing_campaigns', 'marketing_group_id')) {
            Schema::table('marketing_campaigns', function (Blueprint $table) {
                $table->foreignId('marketing_group_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('marketing_groups')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('whatsapp_campaigns') && ! Schema::hasColumn('whatsapp_campaigns', 'marketing_group_id')) {
            Schema::table('whatsapp_campaigns', function (Blueprint $table) {
                $table->foreignId('marketing_group_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('marketing_groups')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('marketing_campaigns') && Schema::hasColumn('marketing_campaigns', 'marketing_group_id')) {
            Schema::table('marketing_campaigns', function (Blueprint $table) {
                $table->dropConstrainedForeignId('marketing_group_id');
            });
        }

        if (Schema::hasTable('whatsapp_campaigns') && Schema::hasColumn('whatsapp_campaigns', 'marketing_group_id')) {
            Schema::table('whatsapp_campaigns', function (Blueprint $table) {
                $table->dropConstrainedForeignId('marketing_group_id');
            });
        }

        Schema::dropIfExists('marketing_groups');
    }
};

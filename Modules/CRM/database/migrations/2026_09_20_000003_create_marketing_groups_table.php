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

        $this->ensureCampaignGroupColumn('marketing_campaigns');
        $this->ensureCampaignGroupColumn('whatsapp_campaigns');
    }

    public function down(): void
    {
        $this->dropCampaignGroupColumn('marketing_campaigns');
        $this->dropCampaignGroupColumn('whatsapp_campaigns');

        Schema::dropIfExists('marketing_groups');
    }

    private function ensureCampaignGroupColumn(string $table): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasTable('marketing_groups')) {
            return;
        }

        if (! Schema::hasColumn($table, 'marketing_group_id')) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->unsignedBigInteger('marketing_group_id')->nullable()->after('user_id');
            });
        }

        if ($this->hasForeignKey($table, 'marketing_group_id')) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) {
            $blueprint->foreign('marketing_group_id')
                ->references('id')
                ->on('marketing_groups')
                ->nullOnDelete();
        });
    }

    private function dropCampaignGroupColumn(string $table): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'marketing_group_id')) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) {
            $blueprint->dropConstrainedForeignId('marketing_group_id');
        });
    }

    private function hasForeignKey(string $table, string $column): bool
    {
        foreach (Schema::getForeignKeys($table) as $foreignKey) {
            if (in_array($column, $foreignKey['columns'], true)) {
                return true;
            }
        }

        return false;
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Modules\User\Services\PermissionSync;

/**
 * Bridge for existing deployed databases after late-September alter
 * migrations were folded into create migrations. Safe no-op on fresh installs.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->ensureUsersMobileNullable();
        $this->ensureVisitors();
        $this->ensureJobPositions();
        $this->ensureAiConversations();
        $this->ensureMarketingGroups();
        app(PermissionSync::class)->sync();
    }

    public function down(): void
    {
        // Irreversible schema sync for existing installs.
    }

    private function ensureUsersMobileNullable(): void
    {
        if (
            ! Schema::hasTable('users')
            || ! Schema::hasColumn('users', 'mobile')
            || Schema::getConnection()->getDriverName() !== 'mysql'
        ) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile')->nullable()->change();
        });
    }

    private function ensureVisitors(): void
    {
        if (! Schema::hasTable('visitors')) {
            return;
        }

        Schema::table('visitors', function (Blueprint $table) {
            if (! Schema::hasColumn('visitors', 'referrer')) {
                $table->string('referrer')->nullable()->after('url');
            }
            if (! Schema::hasColumn('visitors', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('referrer');
            }
        });
    }

    private function ensureJobPositions(): void
    {
        if (! Schema::hasTable('job_positions')) {
            return;
        }

        Schema::table('job_positions', function (Blueprint $table) {
            if (! Schema::hasColumn('job_positions', 'slug')) {
                $table->string('slug')->nullable();
            }
            if (! Schema::hasColumn('job_positions', 'location')) {
                $table->string('location')->nullable();
            }
            if (! Schema::hasColumn('job_positions', 'employment_type')) {
                $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'internship', 'remote'])
                    ->default('full_time');
            }
        });

        DB::table('job_positions')->orderBy('id')->each(function (object $position): void {
            if (filled($position->slug ?? null)) {
                return;
            }

            $base = Str::slug((string) $position->title) ?: 'job-'.$position->id;
            $slug = $base;
            $suffix = 2;

            while (DB::table('job_positions')->where('slug', $slug)->exists()) {
                $slug = $base.'-'.$suffix++;
            }

            DB::table('job_positions')->where('id', $position->id)->update(['slug' => $slug]);
        });

        $this->addIndexIfMissing('job_positions', 'job_positions_slug_unique', 'slug', unique: true);
        $this->addIndexIfMissing('job_positions', 'job_positions_employment_type_index', 'employment_type');
    }

    private function ensureAiConversations(): void
    {
        if (! Schema::hasTable('ai_conversations')) {
            Schema::create('ai_conversations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('title')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'updated_at']);
            });
        }

        if (! Schema::hasTable('ai_messages')) {
            Schema::create('ai_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained('ai_conversations')->cascadeOnDelete();
                $table->string('role', 32);
                $table->longText('content')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['conversation_id', 'created_at']);
            });
        }
    }

    private function ensureMarketingGroups(): void
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

    /**
     * @param  string|list<string>  $columns
     */
    private function addIndexIfMissing(string $table, string $index, string|array $columns, bool $unique = false): void
    {
        if (! Schema::hasTable($table) || Schema::hasIndex($table, $index)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($index, $columns, $unique) {
            if ($unique) {
                $blueprint->unique($columns, $index);

                return;
            }

            $blueprint->index($columns, $index);
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

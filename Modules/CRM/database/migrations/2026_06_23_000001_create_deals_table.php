<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('deals')) {
            if (! Schema::hasTable('deal_service')) {
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

            return;
        }

        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignId('pipeline_stage_id')->constrained('pipeline_stages')->restrictOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->nullOnDelete();
            $table->decimal('value', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->unsignedTinyInteger('probability')->nullable();
            $table->date('expected_close_date')->nullable()->index();
            $table->string('source')->nullable();
            $table->text('description')->nullable();
            $table->text('lost_reason')->nullable();
            $table->enum('status', ['open', 'won', 'lost'])->default('open')->index();
            $table->timestamp('won_at')->nullable();
            $table->timestamp('lost_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'won_at'], 'deals_status_won_at_index');
            $table->index(['status', 'lost_at'], 'deals_status_lost_at_index');
            $table->index(['status', 'assigned_to'], 'deals_status_assigned_to_index');
            $table->index(['status', 'pipeline_stage_id'], 'deals_status_pipeline_stage_id_index');
        });

        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'deal_id')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->foreign('deal_id')->references('id')->on('deals')->nullOnDelete();
            });
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

        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'deal_id')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropForeign(['deal_id']);
            });
        }

        Schema::dropIfExists('deals');
    }
};

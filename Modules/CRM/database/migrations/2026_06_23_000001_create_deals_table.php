<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->foreign('deal_id')->references('id')->on('deals')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['deal_id']);
        });

        Schema::dropIfExists('deals');
    }
};

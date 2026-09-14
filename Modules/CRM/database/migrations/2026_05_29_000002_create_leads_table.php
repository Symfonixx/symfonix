<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('leads')) {
            Schema::table('leads', function (Blueprint $table) {
                if (! Schema::hasColumn('leads', 'custom_fields')) {
                    $table->json('custom_fields')->nullable()->after('meta');
                }
            });

            return;
        }

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('job_title')->nullable();
            $table->string('company_name')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('website')->nullable();
            $table->string('industry', 150)->nullable();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('source', 50)->nullable()->default('website')->index();
            $table->string('status', 50)->nullable()->default('new')->index();
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->nullOnDelete();
            $table->unsignedBigInteger('deal_id')->nullable()->index();
            $table->timestamp('converted_at')->nullable();
            $table->string('project_budget')->nullable();
            $table->string('service_interest')->nullable()->index();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->json('service_matches')->nullable();
            $table->text('problem_statement')->nullable();
            $table->json('chat_transcript')->nullable();
            $table->json('meta')->nullable();
            $table->json('custom_fields')->nullable();
            $table->json('attachments')->nullable();
            $table->boolean('blocked')->default(false);
            $table->string('botman_user_id')->nullable();
            $table->string('botman_driver')->nullable();
            $table->string('locale')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['assigned_to', 'status'], 'leads_assigned_to_status_index');
            $table->index('blocked', 'leads_blocked_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

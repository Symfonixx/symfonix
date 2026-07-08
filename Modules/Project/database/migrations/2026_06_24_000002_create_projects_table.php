<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('projects')) {
            if (! Schema::hasColumn('projects', 'attachments')) {
                Schema::table('projects', function (Blueprint $table) {
                    $table->json('attachments')->nullable()->after('due_date');
                });
            }

            return;
        }

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->foreignId('project_status_id')->constrained('project_statuses')->restrictOnDelete();
            $table->foreignId('deal_id')->nullable()->unique()->constrained('deals')->nullOnDelete();
            $table->decimal('budget', 15, 2)->nullable();
            $table->enum('payment_status', ['unpaid', 'partially_paid', 'fully_paid'])->default('unpaid');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

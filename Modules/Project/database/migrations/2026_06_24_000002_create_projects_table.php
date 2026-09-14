<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (! Schema::hasColumn('projects', 'attachments')) {
                    $table->json('attachments')->nullable()->after('due_date');
                }
                if (! Schema::hasColumn('projects', 'currency')) {
                    $table->string('currency', 3)->default('USD')->after('budget');
                }
                if (! Schema::hasColumn('projects', 'budget_exchange_rate')) {
                    $table->decimal('budget_exchange_rate', 18, 8)
                        ->default(1)
                        ->after('currency')
                        ->comment('Units of system base currency per 1 unit of project currency when budget was set');
                }
                if (! Schema::hasColumn('projects', 'tax_rate_id')) {
                    $table->unsignedBigInteger('tax_rate_id')->nullable()->after('currency');
                }
            });

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
            $table->string('currency', 3)->default('USD');
            $table->unsignedBigInteger('tax_rate_id')->nullable();
            $table->decimal('budget_exchange_rate', 18, 8)
                ->default(1)
                ->comment('Units of system base currency per 1 unit of project currency when budget was set');
            $table->enum('payment_status', ['unpaid', 'partially_paid', 'fully_paid'])->default('unpaid');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->index('payment_status', 'projects_payment_status_index');
            $table->index('due_date', 'projects_due_date_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

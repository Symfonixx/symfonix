<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
                $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
                $table->string('name');
                $table->enum('status', ['active', 'trial', 'paused', 'cancelled', 'expired'])->default('active')->index();
                $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly', 'one_time'])->default('monthly');
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('currency', 3)->default('USD');
                $table->date('starts_at');
                $table->date('ends_at')->nullable()->index();
                $table->date('renewal_at')->nullable()->index();
                $table->boolean('auto_renew')->default(true);
                $table->timestamp('cancelled_at')->nullable();
                $table->text('notes')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('subscription_service')) {
            Schema::create('subscription_service', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
                $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['subscription_id', 'service_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_service');
        Schema::dropIfExists('subscriptions');
    }
};

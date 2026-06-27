<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->date('billing_date');
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['subscription_id', 'billing_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_billings');
    }
};

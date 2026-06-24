<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('company_name')->nullable();
            $table->string('project_budget')->nullable();
            $table->string('service_interest')->nullable()->index();
            $table->json('service_matches')->nullable();
            $table->text('problem_statement')->nullable();
            $table->json('chat_transcript')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('blocked')->default(false);
            $table->string('botman_user_id')->nullable();
            $table->string('botman_driver')->nullable();
            $table->string('locale')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

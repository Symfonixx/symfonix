<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('marketing_campaigns')) {
            if (! Schema::hasColumn('marketing_campaigns', 'status')) {
                Schema::table('marketing_campaigns', function (Blueprint $table) {
                    $table->string('status', 20)->default('pending')->after('recipients_count');
                });
            }

            // Existing installs may still have varchar subject.
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE marketing_campaigns MODIFY subject TEXT NOT NULL');
            }

            return;
        }

        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('subject');
            $table->text('body');
            $table->unsignedInteger('recipients_count')->default(0);
            $table->string('status', 20)->default('pending');
            $table->json('recipient_sources')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_campaigns');
    }
};

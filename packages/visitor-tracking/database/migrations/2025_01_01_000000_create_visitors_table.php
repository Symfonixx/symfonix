<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('visitors')) {
            Schema::table('visitors', function (Blueprint $table) {
                if (! Schema::hasColumn('visitors', 'referrer')) {
                    $table->string('referrer')->nullable()->after('url');
                }
                if (! Schema::hasColumn('visitors', 'user_agent')) {
                    $table->text('user_agent')->nullable()->after('referrer');
                }
            });

            return;
        }

        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->ipAddress('ip')->nullable();
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('device')->nullable();
            $table->string('os')->nullable();
            $table->string('browser')->nullable();
            $table->string('page_title')->nullable();
            $table->string('url')->nullable();
            $table->string('referrer')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};

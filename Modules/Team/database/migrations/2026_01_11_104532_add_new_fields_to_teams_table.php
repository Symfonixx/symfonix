<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('github')->nullable()->after('facebook');
            $table->string('behance')->nullable()->after('github');
            $table->string('resume')->nullable()->after('behance');
            $table->text('key_skills')->nullable()->after('resume');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['github', 'behance', 'resume', 'key_skills']);
        });
    }
};

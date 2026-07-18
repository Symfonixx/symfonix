<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketing_campaigns', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('recipients_count');
        });

        // Existing campaigns were already dispatched before status tracking existed.
        DB::table('marketing_campaigns')->update(['status' => 'finished']);
    }

    public function down(): void
    {
        Schema::table('marketing_campaigns', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

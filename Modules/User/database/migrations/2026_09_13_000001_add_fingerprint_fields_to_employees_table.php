<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedInteger('fingerprint_device_uid')->nullable()->after('status');
            $table->timestamp('fingerprint_enrolled_at')->nullable()->after('fingerprint_device_uid');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['fingerprint_device_uid', 'fingerprint_enrolled_at']);
        });
    }
};

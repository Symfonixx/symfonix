<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix charset for all columns to support UTF-8 (Arabic characters)
        if (Schema::hasTable('firewall_logs')) {
            $connection = DB::connection()->getDriverName();
            
            if ($connection === 'mysql') {
                // Convert the entire table to utf8mb4
                DB::statement("ALTER TABLE `firewall_logs` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                
                // Explicitly ensure all text/varchar columns are utf8mb4
                DB::statement("ALTER TABLE `firewall_logs` MODIFY `request` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL");
                DB::statement("ALTER TABLE `firewall_logs` MODIFY `url` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL");
                DB::statement("ALTER TABLE `firewall_logs` MODIFY `referrer` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL");
                DB::statement("ALTER TABLE `firewall_logs` MODIFY `ip` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");
                DB::statement("ALTER TABLE `firewall_logs` MODIFY `level` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium'");
                DB::statement("ALTER TABLE `firewall_logs` MODIFY `middleware` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to utf8 (not recommended but for rollback purposes)
        if (Schema::hasTable('firewall_logs')) {
            $connection = DB::connection()->getDriverName();
            
            if ($connection === 'mysql') {
                DB::statement("ALTER TABLE `firewall_logs` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");
            }
        }
    }
};

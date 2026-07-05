<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (! Schema::hasColumn('contacts', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('company_id')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('contacts', 'source')) {
                $table->string('source', 50)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('contacts', 'phone2')) {
                $table->string('phone2', 50)->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (Schema::hasColumn('contacts', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
            $table->dropColumn(['source', 'phone2']);
        });
    }
};

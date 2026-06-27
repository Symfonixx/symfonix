<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_forms', function (Blueprint $table) {
            $table->foreignId('lead_id')->nullable()->after('blocked')->constrained('leads')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->after('lead_id')->constrained('contacts')->nullOnDelete();
            $table->timestamp('converted_at')->nullable()->after('contact_id');
        });
    }

    public function down(): void
    {
        Schema::table('contact_forms', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lead_id');
            $table->dropConstrainedForeignId('contact_id');
            $table->dropColumn('converted_at');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (! Schema::hasColumn('leads', 'job_title')) {
                $table->string('job_title')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('leads', 'city')) {
                $table->string('city', 100)->nullable()->after('company_name');
            }
            if (! Schema::hasColumn('leads', 'country')) {
                $table->string('country', 100)->nullable()->after('city');
            }
            if (! Schema::hasColumn('leads', 'website')) {
                $table->string('website')->nullable()->after('country');
            }
            if (! Schema::hasColumn('leads', 'industry')) {
                $table->string('industry', 150)->nullable()->after('website');
            }
            if (! Schema::hasColumn('leads', 'status')) {
                $table->string('status', 50)->nullable()->default('new')->index()->after('source');
            }
            if (! Schema::hasColumn('leads', 'attachments')) {
                $table->json('attachments')->nullable()->after('meta');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'job_title',
                'city',
                'country',
                'website',
                'industry',
                'status',
                'attachments',
            ]);
        });
    }
};

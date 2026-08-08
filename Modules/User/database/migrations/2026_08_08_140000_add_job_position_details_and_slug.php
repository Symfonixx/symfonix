<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('job_positions')) {
            return;
        }

        Schema::table('job_positions', function (Blueprint $table) {
            if (! Schema::hasColumn('job_positions', 'slug')) {
                $table->string('slug')->nullable();
            }

            if (! Schema::hasColumn('job_positions', 'location')) {
                $table->string('location')->nullable();
            }

            if (! Schema::hasColumn('job_positions', 'employment_type')) {
                $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'internship', 'remote'])
                    ->default('full_time');
            }
        });

        DB::table('job_positions')->orderBy('id')->each(function (object $position): void {
            if (filled($position->slug)) {
                return;
            }

            $base = Str::slug($position->title) ?: 'job-'.$position->id;
            $slug = $base;
            $suffix = 2;

            while (DB::table('job_positions')->where('slug', $slug)->exists()) {
                $slug = $base.'-'.$suffix++;
            }

            DB::table('job_positions')->where('id', $position->id)->update(['slug' => $slug]);
        });

        Schema::table('job_positions', function (Blueprint $table) {
            $table->unique('slug');
            $table->index('employment_type');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('job_positions')) {
            return;
        }

        Schema::table('job_positions', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropIndex(['employment_type']);
            $table->dropColumn(['slug', 'location', 'employment_type']);
        });
    }
};

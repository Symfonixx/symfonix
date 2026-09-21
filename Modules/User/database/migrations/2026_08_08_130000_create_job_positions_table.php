<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('job_positions')) {
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

            return;
        }

        Schema::create('job_positions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('department')->index();
            $table->string('location')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'internship', 'remote'])
                ->default('full_time')
                ->index();
            $table->longText('description');
            $table->longText('requirements')->nullable();
            $table->enum('status', ['active', 'closed'])->default('active')->index();
            $table->date('posted_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_positions');
    }
};

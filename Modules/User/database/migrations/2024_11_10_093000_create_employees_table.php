<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (! Schema::hasColumn('employees', 'position')) {
                    $table->string('position')->nullable()->after('mobile');
                }
                if (! Schema::hasColumn('employees', 'resume')) {
                    $table->string('resume')->nullable()->after('position');
                }
                if (! Schema::hasColumn('employees', 'fingerprint_device_uid')) {
                    $table->unsignedInteger('fingerprint_device_uid')->nullable()->after('status');
                }
                if (! Schema::hasColumn('employees', 'fingerprint_enrolled_at')) {
                    $table->timestamp('fingerprint_enrolled_at')->nullable()->after('fingerprint_device_uid');
                }
                if (! Schema::hasColumn('employees', 'user_id')) {
                    $table->foreignId('user_id')
                        ->nullable()
                        ->unique()
                        ->constrained('users')
                        ->nullOnDelete();
                }
            });

            return;
        }

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobile')->unique();
            $table->string('position')->nullable();
            $table->string('resume')->nullable();
            $table->string('img')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('fingerprint_device_uid')->nullable();
            $table->timestamp('fingerprint_enrolled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

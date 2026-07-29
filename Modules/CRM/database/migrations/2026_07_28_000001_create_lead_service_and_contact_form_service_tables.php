<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lead_service')) {
            Schema::create('lead_service', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
                $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['lead_id', 'service_id']);
            });

            DB::table('leads')
                ->whereNotNull('service_id')
                ->orderBy('id')
                ->chunkById(500, function ($leads) {
                    $now = now();
                    DB::table('lead_service')->insertOrIgnore(
                        collect($leads)->map(fn ($lead) => [
                            'lead_id' => $lead->id,
                            'service_id' => $lead->service_id,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ])->all()
                    );
                });
        }

        if (! Schema::hasTable('contact_form_service')) {
            Schema::create('contact_form_service', function (Blueprint $table) {
                $table->id();
                $table->foreignId('contact_form_id')->constrained('contact_forms')->cascadeOnDelete();
                $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['contact_form_id', 'service_id']);
            });

            DB::table('contact_forms')
                ->whereNotNull('service_id')
                ->orderBy('id')
                ->chunkById(500, function ($forms) {
                    $now = now();
                    DB::table('contact_form_service')->insertOrIgnore(
                        collect($forms)->map(fn ($form) => [
                            'contact_form_id' => $form->id,
                            'service_id' => $form->service_id,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ])->all()
                    );
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_form_service');
        Schema::dropIfExists('lead_service');
    }
};

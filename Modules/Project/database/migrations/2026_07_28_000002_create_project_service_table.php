<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_service')) {
            return;
        }

        Schema::create('project_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['project_id', 'service_id']);
        });

        // Seed from the linked deal's services so existing projects start with sensible data.
        if (Schema::hasTable('deal_service')) {
            $now = now();
            DB::table('projects')
                ->whereNotNull('deal_id')
                ->orderBy('id')
                ->chunkById(200, function ($projects) use ($now) {
                    foreach ($projects as $project) {
                        $serviceIds = DB::table('deal_service')
                            ->where('deal_id', $project->deal_id)
                            ->pluck('service_id');

                        if ($serviceIds->isEmpty()) {
                            continue;
                        }

                        DB::table('project_service')->insertOrIgnore(
                            $serviceIds->map(fn ($serviceId) => [
                                'project_id' => $project->id,
                                'service_id' => $serviceId,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ])->all()
                        );
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_service');
    }
};

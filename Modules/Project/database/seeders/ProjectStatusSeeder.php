<?php

namespace Modules\Project\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Project\Models\ProjectStatus;

class ProjectStatusSeeder extends Seeder
{
    /**
     * Seed default agency project workflow statuses.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Planning', 'color_code' => '#6c757d', 'sort_order' => 1],
            ['name' => 'Development', 'color_code' => '#0d6efd', 'sort_order' => 2],
            ['name' => 'QA', 'color_code' => '#ffc107', 'sort_order' => 3],
            ['name' => 'Completed', 'color_code' => '#198754', 'sort_order' => 4],
            ['name' => 'On Hold', 'color_code' => '#dc3545', 'sort_order' => 5],
        ];

        foreach ($statuses as $status) {
            ProjectStatus::query()->firstOrCreate(
                ['name' => $status['name']],
                $status
            );
        }
    }
}

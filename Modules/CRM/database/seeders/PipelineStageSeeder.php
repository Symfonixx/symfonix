<?php

namespace Modules\CRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CRM\Models\PipelineStage;

class PipelineStageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            [
                'name' => 'Lead',
                'slug' => 'lead',
                'color' => 'primary',
                'sort_order' => 1,
                'probability' => 10,
                'is_won' => false,
                'is_lost' => false,
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Qualified',
                'slug' => 'qualified',
                'color' => 'info',
                'sort_order' => 2,
                'probability' => 25,
                'is_won' => false,
                'is_lost' => false,
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Proposal',
                'slug' => 'proposal',
                'color' => 'warning',
                'sort_order' => 3,
                'probability' => 50,
                'is_won' => false,
                'is_lost' => false,
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Negotiation',
                'slug' => 'negotiation',
                'color' => 'dark',
                'sort_order' => 4,
                'probability' => 75,
                'is_won' => false,
                'is_lost' => false,
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Closed Won',
                'slug' => 'closed-won',
                'color' => 'success',
                'sort_order' => 5,
                'probability' => 100,
                'is_won' => true,
                'is_lost' => false,
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Closed Lost',
                'slug' => 'closed-lost',
                'color' => 'danger',
                'sort_order' => 6,
                'probability' => 0,
                'is_won' => false,
                'is_lost' => true,
                'is_default' => false,
                'is_active' => true,
            ],
        ];

        foreach ($stages as $stage) {
            PipelineStage::updateOrCreate(['slug' => $stage['slug']], $stage);
        }
    }
}

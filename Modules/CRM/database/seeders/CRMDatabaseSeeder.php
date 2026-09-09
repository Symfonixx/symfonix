<?php

namespace Modules\CRM\Database\Seeders;

use Illuminate\Database\Seeder;

class CRMDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PipelineStageSeeder::class,
            LeadTagSeeder::class,
            LeadCustomFieldSeeder::class,
        ]);
    }
}

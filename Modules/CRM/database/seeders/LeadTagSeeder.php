<?php

namespace Modules\CRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CRM\Models\LeadTag;

class LeadTagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            [
                'name' => [
                    'en' => 'Cold',
                    'ar' => 'بارد',
                    'de' => 'Kalt',
                    'tr' => 'Soğuk',
                ],
                'color' => 'info',
                'sort_order' => 1,
            ],
            [
                'name' => [
                    'en' => 'Warm',
                    'ar' => 'دافئ',
                    'de' => 'Warm',
                    'tr' => 'Ilık',
                ],
                'color' => 'warning',
                'sort_order' => 2,
            ],
            [
                'name' => [
                    'en' => 'Hot',
                    'ar' => 'ساخن',
                    'de' => 'Heiß',
                    'tr' => 'Sıcak',
                ],
                'color' => 'danger',
                'sort_order' => 3,
            ],
        ];

        foreach ($tags as $tag) {
            LeadTag::query()->firstOrCreate(
                ['name->en' => $tag['name']['en']],
                [
                    'name' => $tag['name'],
                    'color' => $tag['color'],
                    'sort_order' => $tag['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}

<?php

namespace Modules\Support\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Support\Models\TicketCategory;

class TicketCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => [
                    'en' => 'Technical Support',
                    'ar' => 'الدعم الفني',
                    'tr' => 'Teknik Destek',
                ],
                'sort_order' => 1,
            ],
            [
                'name' => [
                    'en' => 'Billing & Payments',
                    'ar' => 'الفوترة والمدفوعات',
                    'tr' => 'Faturalama ve Ödemeler',
                ],
                'sort_order' => 2,
            ],
            [
                'name' => [
                    'en' => 'General Inquiry',
                    'ar' => 'استفسار عام',
                    'tr' => 'Genel Soru',
                ],
                'sort_order' => 3,
            ],
            [
                'name' => [
                    'en' => 'Account Management',
                    'ar' => 'إدارة الحساب',
                    'tr' => 'Hesap Yönetimi',
                ],
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $category) {
            TicketCategory::query()->firstOrCreate(
                ['name->en' => $category['name']['en']],
                [
                    'name' => $category['name'],
                    'is_active' => true,
                    'sort_order' => $category['sort_order'],
                ]
            );
        }
    }
}

<?php

namespace Modules\CRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CRM\Models\LeadCustomField;

class LeadCustomFieldSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            [
                'key' => 'preferred_contact_time',
                'label' => [
                    'en' => 'Preferred Contact Time',
                    'ar' => 'وقت التواصل المفضل',
                    'de' => 'Bevorzugte Kontaktzeit',
                    'tr' => 'Tercih Edilen İletişim Zamanı',
                ],
                'type' => LeadCustomField::TYPE_SELECT,
                'options' => ['Morning', 'Afternoon', 'Evening'],
                'is_required' => false,
                'sort_order' => 1,
            ],
            [
                'key' => 'decision_maker',
                'label' => [
                    'en' => 'Decision Maker',
                    'ar' => 'صاحب القرار',
                    'de' => 'Entscheidungsträger',
                    'tr' => 'Karar Verici',
                ],
                'type' => LeadCustomField::TYPE_CHECKBOX,
                'options' => null,
                'is_required' => false,
                'sort_order' => 2,
            ],
        ];

        foreach ($fields as $field) {
            LeadCustomField::query()->firstOrCreate(
                ['key' => $field['key']],
                [
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'options' => $field['options'],
                    'is_required' => $field['is_required'],
                    'sort_order' => $field['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}

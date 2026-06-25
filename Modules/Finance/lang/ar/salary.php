<?php

return [
    'menu' => 'الرواتب',
    'pages' => [
        'index_title' => 'إدارة الرواتب',
    ],
    'fields' => [
        'employee' => 'الموظف',
        'base_salary' => 'الراتب الأساسي',
        'status' => 'الحالة',
        'paid_at' => 'تاريخ الدفع',
    ],
    'status' => [
        'pending' => 'معلق',
        'paid' => 'مدفوع',
    ],
    'actions' => [
        'add' => 'إضافة سجل راتب',
        'record_payout' => 'تسجيل الدفع',
    ],
    'messages' => [
        'created' => 'تم إنشاء سجل الراتب.',
        'paid' => 'تم تسجيل دفع الراتب في الدفتر.',
        'payout_description' => 'دفع راتب: :name',
        'no_pending' => 'لا توجد مدفوعات رواتب معلقة.',
    ],
];

<?php

return [
    'menu' => 'الذمم المدينة',
    'pages' => [
        'index_title' => 'أعمار الذمم المدينة',
    ],
    'metrics' => [
        'total_outstanding' => 'إجمالي المستحق',
        'open_invoices' => 'الفواتير المفتوحة',
    ],
    'buckets' => [
        'current' => 'حالي',
        'days_1_30' => '1–30 يوم',
        'days_31_60' => '31–60 يوم',
        'days_61_90' => '61–90 يوم',
        'over_90' => 'أكثر من 90 يوم',
    ],
    'fields' => [
        'invoice_number' => 'رقم الفاتورة',
        'company' => 'الشركة',
        'due_at' => 'تاريخ الاستحقاق',
        'days_overdue' => 'أيام التأخير',
        'amount' => 'المبلغ',
        'status' => 'الحالة',
    ],
    'empty' => 'لا توجد فواتير مفتوحة.',
];

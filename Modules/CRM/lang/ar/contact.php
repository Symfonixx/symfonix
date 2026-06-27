<?php

return [
    'menu' => [
        'contacts' => 'جهات الاتصال',
    ],
    'pages' => [
        'index_title' => 'جهات الاتصال',
        'create_title' => 'إضافة جهة اتصال',
        'edit_title' => 'تعديل جهة اتصال',
        'show_title' => 'تفاصيل جهة الاتصال',
    ],
    'fields' => [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'phone' => 'الهاتف',
        'job_title' => 'المسمى الوظيفي',
        'company' => 'الشركة',
        'select_company' => 'اختر شركة',
        'notes' => 'ملاحظات',
        'is_primary' => 'جهة الاتصال الرئيسية',
    ],
    'actions' => [
        'add' => 'إضافة جهة اتصال',
        'back_to_list' => 'العودة إلى جهات الاتصال',
    ],
    'search' => [
        'placeholder' => 'البحث في جهات الاتصال',
    ],
    'sections' => [
        'basic_information' => 'المعلومات الأساسية',
        'basic_information_hint' => 'شخص CRM. ربط الشركة اختياري.',
        'company' => 'الشركة',
        'company_hint' => 'ربط اختياري بشركة موجودة.',
        'additional' => 'تفاصيل إضافية',
    ],
    'placeholders' => [
        'name' => 'مثال: أحمد محمد',
        'email' => 'name@example.com',
        'phone' => '+966 50 000 0000',
        'job_title' => 'مثال: مدير تقني',
        'notes' => 'ملاحظات داخلية...',
    ],
    'hints' => [
        'company' => 'اتركه فارغاً للجهات غير المرتبطة بشركة.',
        'is_primary' => 'تعيين كجهة الاتصال الرئيسية للشركة.',
    ],
    'validation' => [
        'fix_errors' => 'يرجى تصحيح الأخطاء التالية والمحاولة مرة أخرى.',
    ],
    'fallback_name' => 'جهة اتصال غير معروفة',
];

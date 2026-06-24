<?php

return [
    'menu' => [
        'contacts' => 'جهات الاتصال',
    ],
    'pages' => [
        'index_title' => 'جهات الاتصال',
        'create_title' => 'إضافة جهة اتصال',
        'edit_title' => 'تعديل جهة اتصال',
    ],
    'fields' => [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'mobile' => 'الجوال',
        'subject' => 'الموضوع',
        'message' => 'الرسالة',
        'company' => 'الشركة المرتبطة',
        'select_company' => 'اختر شركة',
        'ip_address' => 'عنوان IP',
        'blocked' => 'محظور',
    ],
    'status' => [
        'active' => 'نشط',
        'blocked' => 'محظور',
    ],
    'actions' => [
        'add' => 'إضافة جهة اتصال',
        'edit' => 'تعديل جهة اتصال',
        'view_details' => 'عرض التفاصيل',
        'back_to_list' => 'العودة إلى جهات الاتصال',
    ],
    'search' => [
        'placeholder' => 'البحث في جهات الاتصال',
    ],
    'sections' => [
        'contact_information' => 'معلومات الاتصال',
        'contact_information_hint' => 'تفاصيل الاتصال الأساسية.',
        'company' => 'الشركة',
        'company_hint' => 'ربط جهة الاتصال بشركة موجودة في نظام CRM (اختياري).',
        'message' => 'الرسالة',
        'status' => 'الحالة',
    ],
    'placeholders' => [
        'name' => 'مثال: أحمد محمد',
        'email' => 'name@example.com',
        'mobile' => '+966 50 000 0000',
        'subject' => 'مثال: استفسار عن مشروع',
        'message' => 'أدخل رسالة أو ملاحظات جهة الاتصال...',
    ],
    'hints' => [
        'company' => 'اختياري. اتركه فارغاً إذا لم تكن جهة الاتصال مرتبطة بشركة.',
        'blocked' => 'جهات الاتصال المحظورة يتم تمييزها في النظام.',
    ],
    'validation' => [
        'fix_errors' => 'يرجى تصحيح الأخطاء التالية والمحاولة مرة أخرى.',
    ],
];

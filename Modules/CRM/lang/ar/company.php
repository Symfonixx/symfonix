<?php

return [
    'menu' => [
        'crm' => 'إدارة العملاء',
        'companies' => 'الشركات',
    ],
    'pages' => [
        'index_title' => 'الشركات',
        'create_title' => 'إضافة شركة جديدة',
        'edit_title' => 'تعديل الشركة',
        'show_title' => 'تفاصيل الشركة',
    ],
    'fields' => [
        'id' => 'المعرف',
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'phone' => 'الهاتف',
        'customer' => 'العميل',
        'select_customer' => 'اختر العميل',
        'country' => 'الدولة',
        'city' => 'المدينة',
        'address' => 'العنوان',
        'notes' => 'ملاحظات',
        'status' => 'الحالة',
    ],
    'status' => [
        'active' => 'نشط',
        'disabled' => 'معطل',
    ],
    'actions' => [
        'add' => 'إضافة شركة',
        'back_to_list' => 'العودة إلى الشركات',
    ],
    'search' => [
        'placeholder' => 'البحث في الشركات',
    ],
    'sections' => [
        'basic_information' => 'المعلومات الأساسية',
        'basic_information_hint' => 'ابدأ ببيانات الشركة الأساسية واربطها بالعميل المناسب.',
        'contact_information' => 'معلومات التواصل',
        'location' => 'الموقع',
        'location_hint' => 'هذه الحقول تساعد فريقك على معرفة مكان الشركة بدقة.',
        'additional_details' => 'تفاصيل إضافية',
        'additional_details_hint' => 'أضف أي تفاصيل تساعد فريقك في المتابعة والدعم.',
    ],
    'placeholders' => [
        'name' => 'مثال: شركة أكمي للتقنية',
        'email' => 'name@company.com',
        'phone' => '+966 5X XXX XXXX',
        'country' => 'مثال: المملكة العربية السعودية',
        'city' => 'مثال: الرياض',
        'address' => 'الشارع، المبنى، رقم المكتب',
        'notes' => 'ملاحظات اختيارية للاستخدام الداخلي',
    ],
    'hints' => [
        'customer' => 'اختر حساب العميل المرتبط بهذه الشركة.',
        'status' => 'الشركات المعطلة تبقى محفوظة لكنها لا تظهر ضمن العمليات النشطة.',
    ],
    'validation' => [
        'fix_errors' => 'يرجى تصحيح الأخطاء التالية ثم المحاولة مرة أخرى.',
    ],
];

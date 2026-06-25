<?php

return [
    'menu' => [
        'products' => 'المنتجات',
        'catalog' => 'الكتالوج',
    ],
    'pages' => [
        'index_title' => 'كتالوج المنتجات',
        'create_title' => 'إضافة منتج',
        'edit_title' => 'تعديل منتج',
    ],
    'fields' => [
        'name' => 'اسم المنتج',
        'category' => 'الفئة',
        'select_category' => 'اختر الفئة',
        'sku' => 'رمز المنتج',
        'description' => 'الوصف',
        'price' => 'السعر',
        'billing' => 'الفوترة',
        'status' => 'الحالة',
        'featured' => 'مميز',
        'featured_help' => 'إبراز في الكتالوج والعروض',
    ],
    'billing' => [
        'one_time' => 'مرة واحدة',
        'monthly' => 'شهري',
        'quarterly' => 'ربع سنوي',
        'yearly' => 'سنوي',
    ],
    'status' => [
        'active' => 'نشط',
        'archived' => 'مؤرشف',
    ],
    'filters' => [
        'title' => 'التصفية',
        'all_categories' => 'جميع الفئات',
        'all_statuses' => 'جميع الحالات',
    ],
    'search' => [
        'placeholder' => 'ابحث بالاسم أو الرمز أو الوصف',
    ],
    'actions' => [
        'add' => 'إضافة منتج',
        'apply_filters' => 'تطبيق',
        'clear_filters' => 'مسح',
    ],
    'messages' => [
        'archived_instead' => 'المنتج له سجل مبيعات وتم أرشفته بدلاً من حذفه.',
    ],
    'errors' => [
        'has_sales' => 'لا يمكن حذف منتج له مبيعات مسجلة.',
    ],
];

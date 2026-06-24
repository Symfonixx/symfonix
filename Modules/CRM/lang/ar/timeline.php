<?php

return [
    'title' => 'سجل النشاط',
    'add_activity' => 'تسجيل نشاط',
    'no_entries' => 'لا توجد أنشطة أو سجلات تدقيق بعد.',
    'by' => 'بواسطة',
    'system' => 'النظام',
    'fields_changed' => 'الحقول المتغيرة',
    'delete_activity' => 'حذف النشاط',
    'activity_types' => [
        'note' => 'ملاحظة',
        'call' => 'مكالمة',
        'meeting' => 'اجتماع',
        'task' => 'مهمة',
        'email' => 'بريد إلكتروني',
    ],
    'fields' => [
        'type' => 'النوع',
        'title' => 'العنوان',
        'body' => 'التفاصيل',
        'scheduled_at' => 'موعد مجدول',
        'completed_at' => 'تاريخ الإنجاز',
    ],
    'placeholders' => [
        'title' => 'عنوان قصير اختياري',
        'body' => 'ماذا حدث؟ أضف سياقاً لفريقك...',
    ],
    'audit' => [
        'created' => 'تم إنشاء :name',
        'updated' => 'تم تحديث :name',
        'deleted' => 'تم حذف :name',
        'stage_changed' => 'تغيير المرحلة من :from إلى :to',
        'activity_logged' => 'تم تسجيل نشاط: :type',
        'activity_removed' => 'تم حذف نشاط: :type',
        'converted' => 'تم التحويل إلى صفقة',
    ],
    'events' => [
        'created' => 'إنشاء',
        'updated' => 'تحديث',
        'deleted' => 'حذف',
        'stage_changed' => 'تغيير مرحلة',
        'activity_logged' => 'تسجيل نشاط',
        'activity_removed' => 'حذف نشاط',
        'converted' => 'تحويل',
        'restored' => 'استعادة',
        'activity_logged' => 'تسجيل نشاط',
        'activity_removed' => 'حذف نشاط',
    ],
    'errors' => [
        'invalid_subject' => 'نوع الكيان غير صالح.',
    ],
];

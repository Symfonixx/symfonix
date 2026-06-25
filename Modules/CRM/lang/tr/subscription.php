<?php

return [
    'menu' => [
        'subscriptions' => 'Abonelikler',
    ],
    'pages' => [
        'index_title' => 'Abonelikler',
        'create_title' => 'Abonelik Ekle',
        'edit_title' => 'Aboneliği Düzenle',
        'show_title' => 'Abonelik Detayları',
    ],
    'fields' => [
        'name' => 'Plan Adı',
        'company' => 'Şirket',
        'select_company' => 'Şirket seçin',
        'product' => 'Ürün',
        'select_product' => 'Ürün seçin (isteğe bağlı)',
        'status' => 'Durum',
        'billing_cycle' => 'Faturalama Döngüsü',
        'amount' => 'Tutar',
        'currency' => 'Para Birimi',
        'starts_at' => 'Başlangıç Tarihi',
        'ends_at' => 'Bitiş Tarihi',
        'renewal_at' => 'Sonraki Yenileme',
        'auto_renew' => 'Otomatik Yenile',
        'notes' => 'Notlar',
    ],
    'status' => [
        'active' => 'Aktif',
        'trial' => 'Deneme',
        'paused' => 'Duraklatıldı',
        'cancelled' => 'İptal Edildi',
        'expired' => 'Süresi Doldu',
    ],
    'billing_cycle' => [
        'monthly' => 'Aylık',
        'quarterly' => 'Üç Aylık',
        'yearly' => 'Yıllık',
        'one_time' => 'Tek Seferlik',
    ],
    'actions' => [
        'add' => 'Abonelik Ekle',
        'add_for_company' => 'Abonelik Ekle',
        'back_to_list' => 'Aboneliklere Dön',
        'view_all' => 'Tüm Abonelikleri Gör',
        'renewing_soon' => 'Yakında Yenilenecek',
    ],
    'search' => [
        'placeholder' => 'Aboneliklerde ara',
    ],
    'sections' => [
        'basic_information' => 'Abonelik Detayları',
        'basic_information_hint' => 'Bu aboneliği bir şirkete bağlayın ve planı tanımlayın.',
        'billing' => 'Faturalama',
        'billing_hint' => 'Tutar, döngü ve yenileme takvimi.',
        'dates' => 'Tarihler',
        'dates_hint' => 'Başlangıç, bitiş ve sonraki yenileme tarihleri.',
        'company_subscriptions' => 'Abonelikler',
    ],
    'placeholders' => [
        'name' => 'Örnek: Premium Destek Planı',
        'amount' => '0.00',
        'notes' => 'Bu abonelik hakkında dahili notlar',
    ],
    'hints' => [
        'renewal_at' => 'Başlangıç tarihi ve faturalama döngüsünden otomatik hesaplamak için boş bırakın.',
        'auto_renew' => 'Yenileme tarihi geldiğinde otomatik yenile.',
        'ends_at' => 'Abonelik için isteğe bağlı kesin bitiş tarihi.',
        'product' => 'Bu aboneliği isteğe bağlı olarak katalog ürününe bağlayın.',
    ],
    'empty' => [
        'company' => 'Bu şirket için henüz abonelik yok.',
    ],
    'validation' => [
        'fix_errors' => 'Lütfen aşağıdaki hataları düzeltin ve tekrar deneyin.',
    ],
];

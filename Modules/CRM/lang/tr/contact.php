<?php

return [
    'menu' => [
        'contacts' => 'Kişiler',
    ],
    'pages' => [
        'index_title' => 'Kişiler',
        'create_title' => 'Yeni Kişi Ekle',
        'edit_title' => 'Kişiyi Düzenle',
    ],
    'fields' => [
        'name' => 'Ad',
        'email' => 'E-posta',
        'mobile' => 'Telefon',
        'subject' => 'Konu',
        'message' => 'Mesaj',
        'company' => 'Bağlı Şirket',
        'select_company' => 'Şirket seçin',
        'ip_address' => 'IP Adresi',
        'blocked' => 'Engelli',
    ],
    'status' => [
        'active' => 'Aktif',
        'blocked' => 'Engelli',
    ],
    'actions' => [
        'add' => 'Kişi Ekle',
        'edit' => 'Kişiyi Düzenle',
        'view_details' => 'Detayları Gör',
        'back_to_list' => 'Kişilere Dön',
    ],
    'search' => [
        'placeholder' => 'Kişilerde ara',
    ],
    'sections' => [
        'contact_information' => 'İletişim Bilgileri',
        'contact_information_hint' => 'Temel iletişim bilgileri.',
        'company' => 'Şirket',
        'company_hint' => 'Bu kişiyi isteğe bağlı olarak mevcut bir CRM şirketine bağlayın.',
        'message' => 'Mesaj',
        'status' => 'Durum',
    ],
    'placeholders' => [
        'name' => 'Örnek: Ahmet Yılmaz',
        'email' => 'name@example.com',
        'mobile' => '+90 555 000 0000',
        'subject' => 'Örnek: Proje talebi',
        'message' => 'Kişi mesajını veya notlarını girin...',
    ],
    'hints' => [
        'company' => 'İsteğe bağlı. Kişi bir şirkete bağlı değilse boş bırakın.',
        'blocked' => 'Engellenen kişiler sistemde işaretlenir.',
    ],
    'validation' => [
        'fix_errors' => 'Lütfen aşağıdaki hataları düzeltip tekrar deneyin.',
    ],
];

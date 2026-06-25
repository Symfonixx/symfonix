<?php

return [
    'menu' => 'Maaşlar',
    'pages' => [
        'index_title' => 'Maaş Yönetimi',
    ],
    'fields' => [
        'employee' => 'Çalışan',
        'base_salary' => 'Temel Maaş',
        'status' => 'Durum',
        'paid_at' => 'Ödeme Tarihi',
    ],
    'status' => [
        'pending' => 'Beklemede',
        'paid' => 'Ödendi',
    ],
    'actions' => [
        'add' => 'Maaş Kaydı Ekle',
        'record_payout' => 'Ödemeyi Kaydet',
    ],
    'messages' => [
        'created' => 'Maaş kaydı oluşturuldu.',
        'paid' => 'Maaş ödemesi deftere kaydedildi.',
        'payout_description' => 'Maaş ödemesi: :name',
        'no_pending' => 'Bekleyen maaş ödemesi yok.',
    ],
];

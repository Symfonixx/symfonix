<?php

return [
    'menu' => 'Ürün Satışları',
    'pages' => [
        'index_title' => 'Ürün Satışları',
    ],
    'fields' => [
        'product' => 'Ürün',
        'select_product' => 'Ürün seçin',
        'company' => 'Şirket',
        'quantity' => 'Adet',
        'unit_price' => 'Birim Fiyat',
        'default_price' => 'Katalog fiyatı',
        'total' => 'Toplam',
        'sold_at' => 'Satış Tarihi',
        'deal' => 'Anlaşma',
    ],
    'actions' => [
        'record_sale' => 'Ürün Satışı Kaydet',
    ],
    'messages' => [
        'recorded' => 'Ürün satışı finans defterine kaydedildi.',
        'deleted' => 'Ürün satışı ve defter kaydı kaldırıldı.',
        'confirm_delete' => 'Bu ürün satışını silmek ve finans defterinden kaldırmak istiyor musunuz?',
        'income' => 'Ürün satışı: :product (×:quantity)',
        'no_sales' => 'Henüz ürün satışı kaydedilmedi.',
    ],
];

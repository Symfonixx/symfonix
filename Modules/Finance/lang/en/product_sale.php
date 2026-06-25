<?php

return [
    'menu' => 'Product Sales',
    'pages' => [
        'index_title' => 'Product Sales',
    ],
    'fields' => [
        'product' => 'Product',
        'select_product' => 'Select product',
        'company' => 'Company',
        'quantity' => 'Qty',
        'unit_price' => 'Unit Price',
        'default_price' => 'Catalog price',
        'total' => 'Total',
        'sold_at' => 'Sale Date',
        'deal' => 'Deal',
    ],
    'actions' => [
        'record_sale' => 'Record Product Sale',
    ],
    'messages' => [
        'recorded' => 'Product sale recorded in the finance ledger.',
        'deleted' => 'Product sale and its ledger entry were removed.',
        'confirm_delete' => 'Delete this product sale and remove it from the finance ledger?',
        'income' => 'Product sale: :product (×:quantity)',
        'no_sales' => 'No product sales recorded yet.',
    ],
];

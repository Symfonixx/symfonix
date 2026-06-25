<?php

return [
    'menu' => [
        'products' => 'Products',
        'catalog' => 'Catalog',
    ],
    'pages' => [
        'index_title' => 'Product Catalog',
        'create_title' => 'Add Product',
        'edit_title' => 'Edit Product',
    ],
    'fields' => [
        'name' => 'Product Name',
        'category' => 'Category',
        'select_category' => 'Select category',
        'sku' => 'SKU',
        'description' => 'Description',
        'price' => 'Price',
        'billing' => 'Billing',
        'status' => 'Status',
        'featured' => 'Featured',
        'featured_help' => 'Highlight on catalog and proposals',
    ],
    'billing' => [
        'one_time' => 'One-time',
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'yearly' => 'Yearly',
    ],
    'status' => [
        'active' => 'Active',
        'archived' => 'Archived',
    ],
    'filters' => [
        'title' => 'Filters',
        'all_categories' => 'All categories',
        'all_statuses' => 'All statuses',
    ],
    'search' => [
        'placeholder' => 'Search by name, SKU, or description',
    ],
    'actions' => [
        'add' => 'Add Product',
        'apply_filters' => 'Apply',
        'clear_filters' => 'Clear',
    ],
    'messages' => [
        'archived_instead' => 'Product has sales history and was archived instead of deleted.',
    ],
    'errors' => [
        'has_sales' => 'Cannot delete a product that has recorded sales.',
    ],
];

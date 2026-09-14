<?php

return [
    'name' => 'Reporting',
    'default_period' => 'this_month',
    'chart_colors' => [
        '#3E97FF', '#50CD89', '#FFC700', '#7239EA', '#F1416C', '#181C32', '#A1A5B7',
    ],
    'departments' => [
        'finance' => [
            'permission' => 'reporting.finance.view',
            'route' => 'admin.reporting.finance',
        ],
        'sales' => [
            'permission' => 'reporting.sales.view',
            'route' => 'admin.reporting.sales',
        ],
        'marketing' => [
            'permission' => 'reporting.marketing.view',
            'route' => 'admin.reporting.marketing',
        ],
        'operations' => [
            'permission' => 'reporting.operations.view',
            'route' => 'admin.reporting.operations',
        ],
        'employee' => [
            'permission' => 'reporting.employee.view',
            'route' => 'admin.reporting.employee',
        ],
    ],
];

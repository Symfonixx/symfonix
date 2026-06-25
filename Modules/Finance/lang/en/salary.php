<?php

return [
    'menu' => 'Salaries',
    'pages' => [
        'index_title' => 'Salary Management',
    ],
    'fields' => [
        'employee' => 'Employee',
        'base_salary' => 'Base Salary',
        'status' => 'Status',
        'paid_at' => 'Paid At',
    ],
    'status' => [
        'pending' => 'Pending',
        'paid' => 'Paid',
    ],
    'actions' => [
        'add' => 'Add Salary Record',
        'record_payout' => 'Record Payout',
    ],
    'messages' => [
        'created' => 'Salary record created.',
        'paid' => 'Salary payout recorded in the ledger.',
        'payout_description' => 'Salary payout: :name',
        'no_pending' => 'No pending salary payouts.',
    ],
];

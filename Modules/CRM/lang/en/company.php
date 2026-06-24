<?php

return [
    'menu' => [
        'crm' => 'CRM',
        'companies' => 'Companies',
    ],
    'pages' => [
        'index_title' => 'Companies',
        'create_title' => 'Add New Company',
        'edit_title' => 'Edit Company',
        'show_title' => 'Company Details',
    ],
    'fields' => [
        'id' => 'ID',
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'customer' => 'Customer',
        'select_customer' => 'Select customer',
        'country' => 'Country',
        'city' => 'City',
        'address' => 'Address',
        'notes' => 'Notes',
        'status' => 'Status',
    ],
    'status' => [
        'active' => 'Active',
        'disabled' => 'Disabled',
    ],
    'actions' => [
        'add' => 'Add Company',
        'back_to_list' => 'Back to Companies',
    ],
    'search' => [
        'placeholder' => 'Search in Companies',
    ],
    'sections' => [
        'basic_information' => 'Basic Information',
        'basic_information_hint' => 'Start with core company details and link it to a customer.',
        'contact_information' => 'Contact Information',
        'location' => 'Location',
        'location_hint' => 'These fields help your team identify where the company is based.',
        'additional_details' => 'Additional Details',
        'additional_details_hint' => 'Add context that can help your team in follow-up and support.',
    ],
    'placeholders' => [
        'name' => 'Example: Acme Technologies',
        'email' => 'name@company.com',
        'phone' => '+1 555 123 4567',
        'country' => 'Example: United Arab Emirates',
        'city' => 'Example: Dubai',
        'address' => 'Street, building, office number',
        'notes' => 'Optional notes for internal team use',
    ],
    'hints' => [
        'customer' => 'Choose the customer account that owns this company.',
        'status' => 'Disabled companies remain saved but are hidden from active workflows.',
    ],
    'validation' => [
        'fix_errors' => 'Please fix the following errors and try again.',
    ],
];

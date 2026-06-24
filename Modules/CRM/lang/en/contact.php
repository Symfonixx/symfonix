<?php

return [
    'menu' => [
        'contacts' => 'Contacts',
    ],
    'pages' => [
        'index_title' => 'Contacts',
        'create_title' => 'Add New Contact',
        'edit_title' => 'Edit Contact',
    ],
    'fields' => [
        'name' => 'Name',
        'email' => 'Email',
        'mobile' => 'Mobile',
        'subject' => 'Subject',
        'message' => 'Message',
        'company' => 'Linked Company',
        'select_company' => 'Select a company',
        'ip_address' => 'IP Address',
        'blocked' => 'Blocked',
    ],
    'status' => [
        'active' => 'Active',
        'blocked' => 'Blocked',
    ],
    'actions' => [
        'add' => 'Add Contact',
        'edit' => 'Edit Contact',
        'view_details' => 'View Details',
        'back_to_list' => 'Back to Contacts',
    ],
    'search' => [
        'placeholder' => 'Search in Contacts',
    ],
    'sections' => [
        'contact_information' => 'Contact Information',
        'contact_information_hint' => 'Basic contact details.',
        'company' => 'Company',
        'company_hint' => 'Optionally link this contact to an existing CRM company.',
        'message' => 'Message',
        'status' => 'Status',
    ],
    'placeholders' => [
        'name' => 'Example: John Smith',
        'email' => 'name@example.com',
        'mobile' => '+1 555 000 0000',
        'subject' => 'Example: Project inquiry',
        'message' => 'Enter the contact message or notes...',
    ],
    'hints' => [
        'company' => 'Optional. Leave empty if this contact is not linked to a company.',
        'blocked' => 'Blocked contacts are flagged in the system.',
    ],
    'validation' => [
        'fix_errors' => 'Please fix the following errors and try again.',
    ],
];

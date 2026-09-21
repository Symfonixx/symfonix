<?php

use App\Models\User;
use App\Notifications\SystemEntityCreatedNotification;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Contact;
use Modules\CRM\Models\ContactForm;
use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\Quote;
use Modules\CRM\Models\Subscription;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\Salary;
use Modules\Product\Models\ProductSale;
use Modules\Project\Models\Project;
use Modules\Support\Models\Ticket;
use Modules\User\Models\Employee;
use Modules\User\Models\JobApplication;
use Modules\User\Models\LeaveRequest;

return [
    /*
    |--------------------------------------------------------------------------
    | Entity-created notifications
    |--------------------------------------------------------------------------
    |
    | Maps Eloquent models to the permission that identifies recipients and
    | the queued notification class to send. Recipients are resolved with
    | User::permission($permission) — never hardcoded email addresses.
    |
    */

    'enabled' => (bool) env('ENTITY_CREATED_NOTIFICATIONS_ENABLED', true),

    'exclude_actor' => (bool) env('ENTITY_CREATED_NOTIFICATIONS_EXCLUDE_ACTOR', false),

    /*
    | Send immediately instead of pushing to the queue. Defaults to true in
    | local so Mailtrap and the admin bell update without `queue:work`.
    */
    'send_now' => (bool) env('ENTITY_CREATED_NOTIFICATIONS_SEND_NOW', env('APP_ENV', 'production') === 'local'),

    'queue' => env('NOTIFICATION_QUEUE', 'default'),

    'default_channels' => ['database', 'mail'],

    'default_notification' => SystemEntityCreatedNotification::class,

    /*
    | Skip dispatching while these artisan commands run (seed/migrate/install).
    */
    'quiet_commands' => [
        'db:seed',
        'module:seed',
        'migrate',
        'migrate:fresh',
        'migrate:refresh',
        'migrate:reset',
        'app:install',
    ],

    'events' => [
        Project::class => [
            'created' => [
                'permission' => 'project.projects.view',
                'notification' => SystemEntityCreatedNotification::class,
                'entity' => 'project',
                'route' => 'admin.projects.show',
                'title_attribute' => 'title',
            ],
        ],
        Lead::class => [
            'created' => [
                'permission' => 'crm.leads.view',
                'notification' => SystemEntityCreatedNotification::class,
                'entity' => 'lead',
                'route' => 'admin.leads.show',
                'title_attribute' => 'name',
                'extra_attributes' => [
                    'email',
                    'company_name',
                    'project_budget',
                    'service_interest',
                    'problem_statement',
                ],
            ],
        ],
        Invoice::class => [
            'created' => [
                'permission' => 'finance.invoices.view',
                'notification' => SystemEntityCreatedNotification::class,
                'entity' => 'invoice',
                'route' => 'admin.finance.invoices.show',
                'title_attribute' => 'invoice_number',
            ],
        ],
        CrmActivity::class => [
            'created' => [
                'permission' => 'crm.activities.view',
                'notification' => SystemEntityCreatedNotification::class,
                'entity' => 'task',
                'route' => 'admin.crm.calendar',
                'title_attribute' => 'title',
                'match' => [
                    'type' => CrmActivity::TYPE_TASK,
                ],
            ],
        ],
        Company::class => [
            'created' => [
                'permission' => 'crm.companies.view',
                'notification' => SystemEntityCreatedNotification::class,
                'entity' => 'client',
                'route' => 'admin.companies.show',
                'title_attribute' => 'name',
            ],
        ],
        Ticket::class => [
            'created' => [
                'permission' => 'support.tickets.view',
                'notification' => SystemEntityCreatedNotification::class,
                'entity' => 'ticket',
                'route' => 'admin.tickets.show',
                'title_attribute' => 'ticket_number',
                'extra_attributes' => ['subject'],
            ],
        ],
        Deal::class => [
            'created' => [
                'permission' => 'sales.deals.view',
                'notification' => SystemEntityCreatedNotification::class,
                'entity' => 'deal',
                'route' => 'admin.deals.show',
                'title_attribute' => 'title',
            ],
        ],
        Quote::class => [
            'created' => [
                'permission' => 'sales.quotes.view',
                'notification' => SystemEntityCreatedNotification::class,
                'entity' => 'quote',
                'route' => 'admin.quotes.show',
                'title_attribute' => 'quote_number',
            ],
        ],
        Contact::class => [
            'created' => [
                'permission' => 'crm.contacts.view',
                'notification' => SystemEntityCreatedNotification::class,
                'entity' => 'contact',
                'route' => 'admin.contacts.show',
                'title_attribute' => 'name',
            ],
        ],
        ContactForm::class => [
            'created' => [
                'permission' => 'crm.inquiries.view',
                'notification' => SystemEntityCreatedNotification::class,
                'entity' => 'inquiry',
                'route' => 'admin.contact_forms.index',
                'title_attribute' => 'subject',
                'extra_attributes' => ['name', 'email', 'mobile', 'message'],
            ],
        ],
        Subscription::class => [
            'created' => [
                'permission' => 'sales.subscriptions.view',
                'notification' => SystemEntityCreatedNotification::class,
                'entity' => 'subscription',
                'route' => 'admin.subscriptions.show',
                'title_attribute' => 'name',
            ],
        ],
        Employee::class => [
            'created' => [
                'permission' => 'hr.employees.view',
                'notification' => SystemEntityCreatedNotification::class,
                'entity' => 'employee',
                'route' => 'admin.employees.show',
                'title_attribute' => 'name',
            ],
        ],
        User::class => [
            'created' => [
                'permission' => 'sales.customers.view',
                'notification' => SystemEntityCreatedNotification::class,
                'entity' => 'customer',
                'route' => 'admin.customers.index',
                'title_attribute' => 'name',
                'match' => [
                    'type' => User::TYPE_CUSTOMER,
                ],
            ],
        ],
        JobApplication::class => [
            'created' => [
                'permission' => 'hr.job_applications.view',
                'notification' => SystemEntityCreatedNotification::class,
                'entity' => 'job_application',
                'route' => 'admin.job-applications.show',
                'title_attribute' => 'id',
            ],
        ],
        LeaveRequest::class => [
            'created' => [
                'permission' => 'hr.leaves.view',
                'notification' => SystemEntityCreatedNotification::class,
                'entity' => 'leave',
                'route' => 'admin.leaves.index',
                'title_attribute' => 'type',
            ],
        ],
        Salary::class => [
            'created' => [
                'permission' => 'finance.salaries.view',
                'notification' => SystemEntityCreatedNotification::class,
                'entity' => 'salary',
                'route' => 'admin.finance.salaries.index',
                'title_attribute' => 'period',
            ],
        ],
        ProductSale::class => [
            'created' => [
                'permission' => 'finance.product_sales.view',
                'notification' => SystemEntityCreatedNotification::class,
                'entity' => 'product_sale',
                'route' => 'admin.finance.product-sales.index',
                'title_attribute' => 'total_amount',
            ],
        ],
    ],
];

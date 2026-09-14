<?php

namespace Modules\User\Support;

use Illuminate\Http\Request;

final class PermissionCatalog
{
    public const CRUD_ACTIONS = ['view', 'create', 'edit', 'delete'];

    public const EXTRA_ACTIONS = ['send', 'export', 'approve', 'view_all', 'manage', 'reply', 'restore'];

    /**
     * Admin routes that stay available to every authenticated admin.
     *
     * @var list<string>
     */
    public const EXEMPT_ROUTES = [
        'admin.dashboard.index',
        'admin.profile.index',
        'admin.profile.update',
        'admin.notifications.read',
        'admin.notifications.read-all',
        'admin.display-currency.update',
    ];

    /**
     * @var array<string, string|list<string>>|null
     */
    private static ?array $routeMap = null;

    /**
     * @return list<array{
     *     key: string,
     *     label: string,
     *     legacy: list<string>,
     *     tabs: list<array{key: string, label: string, actions: list<string>, routes: array<string, list<string>>}>
     * }>
     */
    public static function groups(): array
    {
        return [
            [
                'key' => 'overview',
                'label' => 'Overview',
                'legacy' => [],
                'tabs' => [
                    [
                        'key' => 'overview.dashboard',
                        'label' => 'Dashboard',
                        'actions' => ['view'],
                        'routes' => [
                            'view' => ['admin.dashboard.index'],
                        ],
                    ],
                    [
                        'key' => 'overview.crm_analytics',
                        'label' => 'CRM Analytics',
                        'legacy' => ['CRM Management'],
                        'actions' => ['view', 'edit'],
                        'routes' => [
                            'view' => ['admin.crm.dashboard'],
                            'edit' => ['admin.crm.dashboard.layout'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'cms',
                'label' => 'CMS',
                'legacy' => ['CMS Management', 'Testimonials Management', 'Team Management'],
                'tabs' => [
                    [
                        'key' => 'cms.pages',
                        'label' => 'Pages',
                        'legacy' => ['CMS Management'],
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.pages.index'],
                            'create' => ['admin.pages.create', 'admin.pages.store'],
                            'edit' => ['admin.pages.edit', 'admin.pages.update'],
                            'delete' => ['admin.pages.deleteMulti'],
                        ],
                    ],
                    [
                        'key' => 'cms.blog_categories',
                        'label' => 'Blog Categories',
                        'legacy' => ['CMS Management'],
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.blogs_categories.index'],
                            'create' => ['admin.blogs_categories.create', 'admin.blogs_categories.store'],
                            'edit' => ['admin.blogs_categories.edit', 'admin.blogs_categories.update'],
                            'delete' => ['admin.blogs_categories.deleteMulti'],
                        ],
                    ],
                    [
                        'key' => 'cms.blogs',
                        'label' => 'Blogs',
                        'legacy' => ['CMS Management'],
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.blogs.index'],
                            'create' => ['admin.blogs.create', 'admin.blogs.store'],
                            'edit' => ['admin.blogs.edit', 'admin.blogs.update'],
                            'delete' => ['admin.blogs.deleteMulti'],
                        ],
                    ],
                    [
                        'key' => 'cms.faqs',
                        'label' => 'FAQs',
                        'legacy' => ['CMS Management'],
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.faqs.index'],
                            'create' => ['admin.faqs.create', 'admin.faqs.store'],
                            'edit' => ['admin.faqs.edit', 'admin.faqs.update'],
                            'delete' => ['admin.faqs.deleteMulti'],
                        ],
                    ],
                    [
                        'key' => 'cms.clients',
                        'label' => 'Our Clients',
                        'legacy' => ['CMS Management'],
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.clients.index'],
                            'create' => ['admin.clients.create', 'admin.clients.store'],
                            'edit' => ['admin.clients.edit', 'admin.clients.update'],
                            'delete' => ['admin.clients.deleteMulti'],
                        ],
                    ],
                    [
                        'key' => 'cms.file_manager',
                        'label' => 'File Manager',
                        'legacy' => ['CMS Management'],
                        'actions' => ['view'],
                        'routes' => [
                            'view' => ['admin.filemanager.index'],
                        ],
                    ],
                    [
                        'key' => 'cms.testimonials',
                        'label' => 'Testimonials',
                        'legacy' => ['Testimonials Management'],
                        'actions' => ['view', 'edit', 'delete', 'approve'],
                        'routes' => [
                            'view' => ['admin.testimonials.index'],
                            'edit' => ['admin.testimonials.edit', 'admin.testimonials.update', 'admin.testimonials.unpublish'],
                            'delete' => ['admin.testimonials.deleteMulti'],
                            'approve' => ['admin.testimonials.approve'],
                        ],
                    ],
                    [
                        'key' => 'cms.team',
                        'label' => 'Our Team',
                        'legacy' => ['Team Management'],
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.teams.index'],
                            'create' => ['admin.teams.create', 'admin.teams.store', 'admin.employees.add-to-team'],
                            'edit' => ['admin.teams.edit', 'admin.teams.update'],
                            'delete' => ['admin.teams.destroy', 'admin.teams.deleteMulti'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'services',
                'label' => 'Services',
                'legacy' => ['Services Management'],
                'tabs' => [
                    [
                        'key' => 'services.catalog',
                        'label' => 'Services',
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.services.index'],
                            'create' => ['admin.services.create', 'admin.services.store'],
                            'edit' => ['admin.services.edit', 'admin.services.update'],
                            'delete' => ['admin.services.deleteMulti'],
                        ],
                    ],
                    [
                        'key' => 'services.categories',
                        'label' => 'Service Categories',
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.service_categories.index'],
                            'create' => ['admin.service_categories.create', 'admin.service_categories.store'],
                            'edit' => ['admin.service_categories.edit', 'admin.service_categories.update'],
                            'delete' => ['admin.service_categories.deleteMulti'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'crm',
                'label' => 'Relationship Management',
                'legacy' => ['CRM Management', 'Sales Management'],
                'tabs' => [
                    [
                        'key' => 'crm.leads',
                        'label' => 'Leads',
                        'legacy' => ['CRM Management'],
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.leads.index', 'admin.leads.show'],
                            'create' => ['admin.leads.create', 'admin.leads.store', 'admin.leads.convert', 'admin.leads.convertCustomer'],
                            'edit' => ['admin.leads.edit', 'admin.leads.update', 'admin.leads.block', 'admin.leads.unblock'],
                            'delete' => ['admin.leads.destroy', 'admin.leads.deleteMulti'],
                        ],
                    ],
                    [
                        'key' => 'crm.contacts',
                        'label' => 'Contacts',
                        'legacy' => ['CRM Management'],
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.contacts.index', 'admin.contacts.show'],
                            'create' => ['admin.contacts.create', 'admin.contacts.store'],
                            'edit' => ['admin.contacts.edit', 'admin.contacts.update'],
                            'delete' => ['admin.contacts.destroy', 'admin.contacts.deleteMulti'],
                        ],
                    ],
                    [
                        'key' => 'crm.companies',
                        'label' => 'Companies',
                        'legacy' => ['CRM Management'],
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.companies.index', 'admin.companies.show'],
                            'create' => ['admin.companies.create', 'admin.companies.store'],
                            'edit' => ['admin.companies.edit', 'admin.companies.update'],
                            'delete' => ['admin.companies.destroy', 'admin.companies.deleteMulti'],
                        ],
                    ],
                    [
                        'key' => 'sales.customers',
                        'label' => 'Customers',
                        'legacy' => ['Sales Management'],
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.customers.index'],
                            'create' => ['admin.customers.store'],
                            'edit' => ['admin.customers.update'],
                            'delete' => ['admin.customers.destroy'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'sales',
                'label' => 'Sales & Deals',
                'legacy' => ['CRM Management', 'Finance Management'],
                'tabs' => [
                    [
                        'key' => 'sales.pipeline',
                        'label' => 'Pipeline',
                        'legacy' => ['CRM Management'],
                        'actions' => ['view'],
                        'routes' => [
                            'view' => ['admin.deals.index'],
                        ],
                    ],
                    [
                        'key' => 'sales.deals',
                        'label' => 'Deals',
                        'legacy' => ['CRM Management'],
                        'actions' => ['view', 'create', 'edit', 'delete', 'view_all'],
                        'routes' => [
                            'view' => ['admin.deals.index', 'admin.deals.show'],
                            'create' => ['admin.deals.create', 'admin.deals.store'],
                            'edit' => ['admin.deals.edit', 'admin.deals.update', 'admin.deals.moveStage'],
                            'delete' => ['admin.deals.destroy', 'admin.deals.deleteMulti'],
                            'view_all' => [],
                        ],
                    ],
                    [
                        'key' => 'sales.quotes',
                        'label' => 'Quotes',
                        'legacy' => ['CRM Management'],
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.quotes.index', 'admin.quotes.show', 'admin.quotes.pdf'],
                            'create' => ['admin.quotes.create', 'admin.quotes.store', 'admin.quotes.from-deal'],
                            'edit' => ['admin.quotes.edit', 'admin.quotes.update', 'admin.quotes.sent', 'admin.quotes.void'],
                            'delete' => ['admin.quotes.destroy'],
                        ],
                    ],
                    [
                        'key' => 'finance.invoices',
                        'label' => 'Invoices',
                        'legacy' => ['Finance Management'],
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.finance.invoices.index', 'admin.finance.invoices.show', 'admin.finance.invoices.pdf'],
                            'create' => ['admin.finance.invoices.create', 'admin.finance.invoices.store', 'admin.finance.invoices.from-deal'],
                            'edit' => ['admin.finance.invoices.sent', 'admin.finance.invoices.paid', 'admin.finance.invoices.void'],
                            'delete' => ['admin.finance.invoices.destroy'],
                        ],
                    ],
                    [
                        'key' => 'sales.subscriptions',
                        'label' => 'Subscriptions',
                        'legacy' => ['CRM Management'],
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.subscriptions.index', 'admin.subscriptions.show'],
                            'create' => ['admin.subscriptions.create', 'admin.subscriptions.store'],
                            'edit' => ['admin.subscriptions.edit', 'admin.subscriptions.update'],
                            'delete' => ['admin.subscriptions.destroy', 'admin.subscriptions.deleteMulti'],
                        ],
                    ],
                    [
                        'key' => 'marketing.email',
                        'label' => 'Email Campaigns',
                        'legacy' => ['CRM Management'],
                        'actions' => ['view', 'send'],
                        'routes' => [
                            'view' => ['admin.crm.marketing.index', 'admin.crm.marketing.show'],
                            'send' => ['admin.crm.marketing.create', 'admin.crm.marketing.store'],
                        ],
                    ],
                    [
                        'key' => 'marketing.whatsapp',
                        'label' => 'WhatsApp Campaigns',
                        'legacy' => ['CRM Management'],
                        'actions' => ['view', 'send'],
                        'routes' => [
                            'view' => ['admin.crm.marketing.index', 'admin.crm.marketing.whatsapp.show'],
                            'send' => [
                                'admin.crm.marketing.whatsapp.create',
                                'admin.crm.marketing.whatsapp.store',
                                'admin.crm.marketing.whatsapp.templates.variables',
                            ],
                        ],
                    ],
                    [
                        'key' => 'marketing.whatsapp_templates',
                        'label' => 'WhatsApp Templates',
                        'legacy' => ['CRM Management'],
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.crm.marketing.whatsapp-templates.index'],
                            'create' => ['admin.crm.marketing.whatsapp-templates.create', 'admin.crm.marketing.whatsapp-templates.store'],
                            'edit' => ['admin.crm.marketing.whatsapp-templates.edit', 'admin.crm.marketing.whatsapp-templates.update'],
                            'delete' => ['admin.crm.marketing.whatsapp-templates.destroy'],
                        ],
                    ],
                    [
                        'key' => 'sales.forecasts',
                        'label' => 'Sales Forecasts',
                        'legacy' => ['CRM Management'],
                        'actions' => ['view'],
                        'routes' => [
                            'view' => ['admin.crm.sales-forecasts.index', 'admin.crm.sales-forecasts.data'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'project',
                'label' => 'Projects',
                'legacy' => ['Project Management'],
                'tabs' => [
                    [
                        'key' => 'project.projects',
                        'label' => 'Projects',
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.projects.index', 'admin.projects.show'],
                            'create' => ['admin.projects.create', 'admin.projects.store'],
                            'edit' => [
                                'admin.projects.edit',
                                'admin.projects.update',
                                'admin.projects.updateStatus',
                                'admin.projects.employees.store',
                                'admin.projects.employees.finish',
                                'admin.projects.employees.destroy',
                                'admin.projects.expenses.store',
                                'admin.projects.invoices.store',
                            ],
                            'delete' => ['admin.projects.destroy', 'admin.projects.deleteMulti'],
                        ],
                    ],
                    [
                        'key' => 'project.use_cases',
                        'label' => 'Use Cases',
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.project-use-cases.index'],
                            'create' => ['admin.project-use-cases.create', 'admin.project-use-cases.store'],
                            'edit' => ['admin.project-use-cases.edit', 'admin.project-use-cases.update'],
                            'delete' => ['admin.project-use-cases.destroy', 'admin.project-use-cases.deleteMulti'],
                        ],
                    ],
                    [
                        'key' => 'project.statuses',
                        'label' => 'Statuses',
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.project-statuses.index'],
                            'create' => ['admin.project-statuses.create', 'admin.project-statuses.store'],
                            'edit' => ['admin.project-statuses.edit', 'admin.project-statuses.update'],
                            'delete' => ['admin.project-statuses.destroy'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'product',
                'label' => 'Products',
                'legacy' => ['Product Management'],
                'tabs' => [
                    [
                        'key' => 'product.catalog',
                        'label' => 'Catalog',
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.products.index'],
                            'create' => ['admin.products.create', 'admin.products.store'],
                            'edit' => ['admin.products.edit', 'admin.products.update', 'admin.products.toggle-published'],
                            'delete' => ['admin.products.destroy'],
                        ],
                    ],
                    [
                        'key' => 'product.categories',
                        'label' => 'Categories',
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.product-categories.index'],
                            'create' => ['admin.product-categories.create', 'admin.product-categories.store'],
                            'edit' => ['admin.product-categories.edit', 'admin.product-categories.update'],
                            'delete' => ['admin.product-categories.destroy'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'finance',
                'label' => 'Finance',
                'legacy' => ['Finance Management'],
                'tabs' => [
                    [
                        'key' => 'finance.dashboard',
                        'label' => 'Dashboard',
                        'actions' => ['view'],
                        'routes' => [
                            'view' => ['admin.finance.dashboard'],
                        ],
                    ],
                    [
                        'key' => 'finance.daily_log',
                        'label' => 'Daily Log',
                        'actions' => ['view'],
                        'routes' => [
                            'view' => ['admin.finance.daily-log'],
                        ],
                    ],
                    [
                        'key' => 'finance.ar',
                        'label' => 'Accounts Receivable',
                        'actions' => ['view'],
                        'routes' => [
                            'view' => ['admin.finance.accounts-receivable'],
                        ],
                    ],
                    [
                        'key' => 'finance.product_sales',
                        'label' => 'Product Sales',
                        'actions' => ['view', 'create', 'delete'],
                        'routes' => [
                            'view' => ['admin.finance.product-sales.index'],
                            'create' => ['admin.finance.product-sales.store'],
                            'delete' => ['admin.finance.product-sales.destroy'],
                        ],
                    ],
                    [
                        'key' => 'finance.commissions',
                        'label' => 'Commissions',
                        'actions' => ['view', 'edit'],
                        'routes' => [
                            'view' => ['admin.finance.commissions.index'],
                            'edit' => ['admin.finance.commissions.payout'],
                        ],
                    ],
                    [
                        'key' => 'finance.salaries',
                        'label' => 'Salaries',
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.finance.salaries.index'],
                            'create' => ['admin.finance.salaries.store'],
                            'edit' => ['admin.finance.salaries.payout'],
                            'delete' => ['admin.finance.salaries.destroy'],
                        ],
                    ],
                    [
                        'key' => 'finance.expense_categories',
                        'label' => 'Expense Categories',
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.finance.expense-categories.index'],
                            'create' => ['admin.finance.expense-categories.create', 'admin.finance.expense-categories.store'],
                            'edit' => ['admin.finance.expense-categories.edit', 'admin.finance.expense-categories.update'],
                            'delete' => ['admin.finance.expense-categories.destroy'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'tax',
                'label' => 'Tax',
                'legacy' => ['Tax Management', 'Finance Management'],
                'tabs' => [
                    [
                        'key' => 'tax.rates',
                        'label' => 'Tax Rates',
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.tax.rates.index'],
                            'create' => ['admin.tax.rates.create', 'admin.tax.rates.store'],
                            'edit' => ['admin.tax.rates.edit', 'admin.tax.rates.update'],
                            'delete' => ['admin.tax.rates.destroy'],
                        ],
                    ],
                    [
                        'key' => 'tax.ledger',
                        'label' => 'Tax Ledger',
                        'actions' => ['view'],
                        'routes' => [
                            'view' => ['admin.tax.ledger.index'],
                        ],
                    ],
                    [
                        'key' => 'tax.filing',
                        'label' => 'Filing Report',
                        'actions' => ['view', 'export'],
                        'routes' => [
                            'view' => ['admin.tax.reports.filing'],
                            'export' => ['admin.tax.reports.filing.export'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'reporting',
                'label' => 'Reports',
                'legacy' => ['Reporting Management'],
                'tabs' => [
                    [
                        'key' => 'reporting.finance',
                        'label' => 'Finance Reports',
                        'actions' => ['view', 'export'],
                        'routes' => [
                            'view' => ['admin.reporting.finance', 'admin.reporting.finance.data', 'api.reporting.finance'],
                            'export' => ['admin.reporting.finance.export'],
                        ],
                    ],
                    [
                        'key' => 'reporting.sales',
                        'label' => 'Sales Reports',
                        'actions' => ['view', 'export'],
                        'routes' => [
                            'view' => ['admin.reporting.sales', 'admin.reporting.sales.data', 'api.reporting.sales'],
                            'export' => ['admin.reporting.sales.export'],
                        ],
                    ],
                    [
                        'key' => 'reporting.marketing',
                        'label' => 'Marketing Reports',
                        'actions' => ['view', 'export'],
                        'routes' => [
                            'view' => ['admin.reporting.marketing', 'admin.reporting.marketing.data', 'api.reporting.marketing'],
                            'export' => ['admin.reporting.marketing.export'],
                        ],
                    ],
                    [
                        'key' => 'reporting.operations',
                        'label' => 'Operations Reports',
                        'actions' => ['view', 'export'],
                        'routes' => [
                            'view' => ['admin.reporting.operations', 'admin.reporting.operations.data', 'api.reporting.operations'],
                            'export' => ['admin.reporting.operations.export'],
                        ],
                    ],
                    [
                        'key' => 'reporting.employee',
                        'label' => 'Employee Reports',
                        'legacy' => ['Reporting Management', 'Hr Management'],
                        'actions' => ['view', 'export'],
                        'routes' => [
                            'view' => [
                                'admin.reporting.employee',
                                'admin.reporting.employee.data',
                                'admin.reporting.employee.show',
                                'api.reporting.employee',
                            ],
                            'export' => ['admin.reporting.employee.export'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'hr',
                'label' => 'Team Management',
                'legacy' => ['Hr Management'],
                'tabs' => [
                    [
                        'key' => 'hr.employees',
                        'label' => 'Employees',
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.employees.index', 'admin.employees.show'],
                            'create' => ['admin.employees.store', 'admin.job-applications.hire'],
                            'edit' => ['admin.employees.update'],
                            'delete' => ['admin.employees.destroy'],
                        ],
                    ],
                    [
                        'key' => 'hr.fingerprint',
                        'label' => 'Fingerprint',
                        'actions' => ['view', 'manage'],
                        'routes' => [
                            'view' => ['admin.fingerprint.index'],
                            'manage' => [
                                'admin.fingerprint.test-connection',
                                'admin.fingerprint.enroll-all',
                                'admin.fingerprint.sync-attendance',
                                'admin.fingerprint.enroll',
                            ],
                        ],
                    ],
                    [
                        'key' => 'hr.admins',
                        'label' => 'Admins',
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.admins.index', 'admin.admins.show'],
                            'create' => ['admin.admins.store', 'admin.employees.convert-to-admin'],
                            'edit' => ['admin.admins.update'],
                            'delete' => ['admin.admins.destroy'],
                        ],
                    ],
                    [
                        'key' => 'hr.roles',
                        'label' => 'Roles',
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.roles.index', 'admin.roles.show'],
                            'create' => ['admin.roles.create', 'admin.roles.store'],
                            'edit' => [
                                'admin.roles.edit',
                                'admin.roles.update',
                                'admin.roles.assign_users',
                                'admin.roles.remove_user_from_role',
                                'admin.roles.remove_users_from_role',
                            ],
                            'delete' => ['admin.roles.delete_role'],
                        ],
                    ],
                    [
                        'key' => 'hr.leaves',
                        'label' => 'Leave Management',
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.leaves.index'],
                            'create' => ['admin.leaves.store'],
                            'edit' => ['admin.leaves.update'],
                            'delete' => ['admin.leaves.destroy'],
                        ],
                    ],
                    [
                        'key' => 'hr.job_positions',
                        'label' => 'Job Positions',
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.job-positions.index'],
                            'create' => ['admin.job-positions.create', 'admin.job-positions.store'],
                            'edit' => ['admin.job-positions.edit', 'admin.job-positions.update'],
                            'delete' => ['admin.job-positions.destroy'],
                        ],
                    ],
                    [
                        'key' => 'hr.job_applications',
                        'label' => 'Job Applications',
                        'actions' => ['view', 'edit'],
                        'routes' => [
                            'view' => ['admin.job-applications.index', 'admin.job-applications.show'],
                            'edit' => ['admin.job-applications.update'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'support',
                'label' => 'Customer Service & Support',
                'legacy' => ['Support Management', 'CRM Management'],
                'tabs' => [
                    [
                        'key' => 'support.tickets',
                        'label' => 'Tickets',
                        'legacy' => ['Support Management'],
                        'actions' => ['view', 'edit', 'reply'],
                        'routes' => [
                            'view' => ['admin.tickets.index', 'admin.tickets.show'],
                            'edit' => ['admin.tickets.update'],
                            'reply' => ['admin.tickets.reply'],
                        ],
                    ],
                    [
                        'key' => 'support.ticket_categories',
                        'label' => 'Ticket Categories',
                        'legacy' => ['Support Management'],
                        'actions' => ['view', 'create', 'edit'],
                        'routes' => [
                            'view' => ['admin.ticket_categories.index'],
                            'create' => ['admin.ticket_categories.create', 'admin.ticket_categories.store'],
                            'edit' => ['admin.ticket_categories.edit', 'admin.ticket_categories.update'],
                        ],
                    ],
                    [
                        'key' => 'crm.activities',
                        'label' => 'Activities',
                        'legacy' => ['CRM Management'],
                        'actions' => ['view', 'create', 'delete'],
                        'routes' => [
                            'view' => ['admin.crm.calendar', 'admin.crm.calendar.events'],
                            'create' => ['admin.activities.store'],
                            'delete' => ['admin.activities.destroy'],
                        ],
                    ],
                    [
                        'key' => 'crm.inquiries',
                        'label' => 'Inquiries',
                        'legacy' => ['CRM Management'],
                        'actions' => ['view', 'create', 'edit', 'delete', 'export'],
                        'routes' => [
                            'view' => ['admin.contact_forms.index'],
                            'create' => ['admin.contact_forms.create', 'admin.contact_forms.store'],
                            'edit' => [
                                'admin.contact_forms.edit',
                                'admin.contact_forms.update',
                                'admin.contact_forms.convertLead',
                                'admin.contact_forms.convertContact',
                            ],
                            'delete' => ['admin.contact_forms.deleteMulti'],
                            'export' => ['admin.contact_forms.export'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'settings',
                'label' => 'System & Settings',
                'legacy' => ['Settings Management', 'Logs Management', 'CRM Management', 'Support Management', 'App Monitoring'],
                'tabs' => [
                    [
                        'key' => 'crm.sales_targets',
                        'label' => 'Sales Targets',
                        'legacy' => ['CRM Management'],
                        'actions' => ['view', 'edit'],
                        'routes' => [
                            'view' => ['admin.crm.sales-targets.index'],
                            'edit' => ['admin.crm.sales-targets.update'],
                        ],
                    ],
                    [
                        'key' => 'crm.lead_tags',
                        'label' => 'Lead Tags',
                        'legacy' => ['CRM Management'],
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.crm.lead-tags.index'],
                            'create' => ['admin.crm.lead-tags.create', 'admin.crm.lead-tags.store'],
                            'edit' => ['admin.crm.lead-tags.edit', 'admin.crm.lead-tags.update'],
                            'delete' => ['admin.crm.lead-tags.destroy'],
                        ],
                    ],
                    [
                        'key' => 'crm.custom_fields',
                        'label' => 'Custom Fields',
                        'legacy' => ['CRM Management'],
                        'actions' => ['view', 'create', 'edit', 'delete'],
                        'routes' => [
                            'view' => ['admin.crm.custom-fields.index'],
                            'create' => ['admin.crm.custom-fields.create', 'admin.crm.custom-fields.store'],
                            'edit' => ['admin.crm.custom-fields.edit', 'admin.crm.custom-fields.update'],
                            'delete' => ['admin.crm.custom-fields.destroy'],
                        ],
                    ],
                    [
                        'key' => 'settings.website',
                        'label' => 'Website Configurations',
                        'legacy' => ['Settings Management'],
                        'actions' => ['view', 'edit'],
                        'routes' => [
                            'view' => ['admin.settings.index'],
                            'edit' => ['admin.settings.store'],
                        ],
                    ],
                    [
                        'key' => 'settings.system',
                        'label' => 'System Configurations',
                        'legacy' => ['Settings Management'],
                        'actions' => ['view', 'edit'],
                        'routes' => [
                            'view' => ['admin.system-configurations.index'],
                            'edit' => [
                                'admin.system-configurations.store',
                                'admin.system-configurations.fetch-rates',
                                'admin.system-configurations.test-fingerprint',
                            ],
                        ],
                    ],
                    [
                        'key' => 'settings.integrations',
                        'label' => 'Integrations',
                        'legacy' => ['Settings Management'],
                        'actions' => ['view', 'edit'],
                        'routes' => [
                            'view' => ['admin.integrations.index'],
                            'edit' => ['admin.integrations.store'],
                        ],
                    ],
                    [
                        'key' => 'settings.seo',
                        'label' => 'SEO Configurations',
                        'legacy' => ['Settings Management'],
                        'actions' => ['view', 'edit'],
                        'routes' => [
                            'view' => ['admin.seo.index'],
                            'edit' => ['admin.seo.store'],
                        ],
                    ],
                    [
                        'key' => 'settings.backups',
                        'label' => 'Backups',
                        'legacy' => ['Settings Management'],
                        'actions' => ['view', 'create', 'delete', 'restore'],
                        'routes' => [
                            'view' => ['admin.backups.index', 'admin.backups.download'],
                            'create' => ['admin.backups.store', 'admin.backups.import'],
                            'delete' => ['admin.backups.destroy'],
                            'restore' => ['admin.backups.restore'],
                        ],
                    ],
                    [
                        'key' => 'support.visitors',
                        'label' => 'Visitors',
                        'legacy' => ['Support Management'],
                        'actions' => ['view'],
                        'routes' => [
                            'view' => ['admin.visitors.index'],
                        ],
                    ],
                    [
                        'key' => 'support.subscribers',
                        'label' => 'Newsletter Subscribers',
                        'legacy' => ['Support Management'],
                        'actions' => ['view', 'create', 'delete', 'export'],
                        'routes' => [
                            'view' => ['admin.subscribers.index'],
                            'create' => ['admin.subscribers.import', 'admin.subscribers.importSample'],
                            'delete' => ['admin.subscribers.deleteMulti'],
                            'export' => ['admin.subscribers.export'],
                        ],
                    ],
                    [
                        'key' => 'support.search_keywords',
                        'label' => 'Search Keywords',
                        'legacy' => ['Support Management'],
                        'actions' => ['view', 'delete', 'export'],
                        'routes' => [
                            'view' => ['admin.search_keywords.index'],
                            'delete' => ['admin.search_keywords.deleteMulti'],
                            'export' => ['admin.search_keywords.export'],
                        ],
                    ],
                    [
                        'key' => 'system.logs',
                        'label' => 'Logs & Bugs',
                        'legacy' => ['Logs Management'],
                        'actions' => ['view', 'delete'],
                        'routes' => [
                            'view' => ['admin.logs.index', 'admin.logs.show'],
                            'delete' => ['admin.logs.deleteMulti'],
                        ],
                    ],
                    [
                        'key' => 'system.monitoring',
                        'label' => 'App Monitoring',
                        'legacy' => ['App Monitoring'],
                        'actions' => ['view'],
                        'routes' => [
                            'view' => [],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function allKeys(): array
    {
        $keys = [];

        foreach (self::groups() as $group) {
            foreach ($group['tabs'] as $tab) {
                foreach ($tab['actions'] as $action) {
                    $keys[] = self::key($tab['key'], $action);
                }
            }
        }

        return array_values(array_unique($keys));
    }

    public static function key(string $tab, string $action): string
    {
        return $tab.'.'.$action;
    }

    /**
     * @param  string|list<string>  $tabOrPrefix
     * @return list<string>
     */
    public static function keysFor(string|array $tabOrPrefix): array
    {
        $needles = is_array($tabOrPrefix) ? $tabOrPrefix : [$tabOrPrefix];
        $keys = [];

        foreach ($needles as $needle) {
            foreach (self::allKeys() as $key) {
                if ($key === $needle || str_starts_with($key, $needle.'.') || $key === $needle) {
                    $keys[] = $key;
                }
            }
        }

        return array_values(array_unique($keys));
    }

    /**
     * @return list<string>
     */
    public static function keysForSection(string $section): array
    {
        foreach (self::groups() as $group) {
            if ($group['key'] !== $section) {
                continue;
            }

            $keys = [];
            foreach ($group['tabs'] as $tab) {
                foreach ($tab['actions'] as $action) {
                    $keys[] = self::key($tab['key'], $action);
                }
            }

            return $keys;
        }

        return self::keysFor($section);
    }

    /**
     * @return list<string>
     */
    public static function expandLegacy(string $legacyName): array
    {
        if ($legacyName === 'CRM View All') {
            return ['sales.deals.view_all'];
        }

        $keys = [];

        foreach (self::groups() as $group) {
            foreach ($group['tabs'] as $tab) {
                $legacyForTab = $tab['legacy'] ?? $group['legacy'];
                if (! in_array($legacyName, $legacyForTab, true)) {
                    continue;
                }

                foreach ($tab['actions'] as $action) {
                    $keys[] = self::key($tab['key'], $action);
                }
            }
        }

        return array_values(array_unique($keys));
    }

    /**
     * @return list<string>
     */
    public static function keysForLegacyRole(array $legacyNames): array
    {
        $keys = ['overview.dashboard.view'];

        foreach ($legacyNames as $name) {
            $keys = array_merge($keys, self::expandLegacy($name));
        }

        return array_values(array_unique($keys));
    }

    public static function isExemptRoute(string $routeName): bool
    {
        return in_array($routeName, self::EXEMPT_ROUTES, true);
    }

    /**
     * @param  object{can: callable, canany: callable}|null  $user
     */
    public static function userMay(?object $user, ?string $routeName, ?Request $request = null): bool
    {
        if ($user === null || $routeName === null || $routeName === '') {
            return false;
        }

        if (self::isExemptRoute($routeName)) {
            return true;
        }

        if (self::isFileManagerRequest($request)) {
            return $user->can('cms.file_manager.view');
        }

        $permission = self::permissionForRoute($routeName, $request);

        if ($permission === null) {
            return true;
        }

        return is_array($permission)
            ? $user->canany($permission)
            : $user->can($permission);
    }

    /**
     * @return string|list<string>|null
     */
    public static function permissionForRoute(string $routeName, ?Request $request = null): string|array|null
    {
        $request ??= request();

        if ($routeName === 'admin.deals.index' && $request->query('view') === 'kanban') {
            return ['sales.pipeline.view', 'sales.deals.view'];
        }

        if ($routeName === 'admin.crm.marketing.index' && $request->query('channel') === 'whatsapp') {
            return 'marketing.whatsapp.view';
        }

        if ($routeName === 'admin.crm.marketing.index') {
            return ['marketing.email.view', 'marketing.whatsapp.view'];
        }

        return self::routeMap()[$routeName] ?? null;
    }

    public static function isFileManagerRequest(?Request $request = null): bool
    {
        $request ??= request();
        $path = $request->path();
        $name = $request->route()?->getName() ?? '';

        return str_contains($path, 'laravel-filemanager')
            || str_contains($name, 'unisharp.lfm')
            || str_contains($name, 'filemanager');
    }

    public static function groupLabel(string $key): string
    {
        $translation = __('user::permissions.groups.'.$key);

        return $translation === 'user::permissions.groups.'.$key
            ? (self::groupByKey($key)['label'] ?? $key)
            : $translation;
    }

    public static function tabLabel(string $key): string
    {
        $translation = __('user::permissions.tabs.'.$key);

        if ($translation !== 'user::permissions.tabs.'.$key) {
            return $translation;
        }

        foreach (self::groups() as $group) {
            foreach ($group['tabs'] as $tab) {
                if ($tab['key'] === $key) {
                    return $tab['label'];
                }
            }
        }

        return $key;
    }

    public static function actionLabel(string $action): string
    {
        $translation = __('user::permissions.actions.'.$action);

        return $translation === 'user::permissions.actions.'.$action
            ? ucfirst(str_replace('_', ' ', $action))
            : $translation;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function groupByKey(string $key): ?array
    {
        foreach (self::groups() as $group) {
            if ($group['key'] === $key) {
                return $group;
            }
        }

        return null;
    }

    /**
     * @return array<string, string|list<string>>
     */
    private static function routeMap(): array
    {
        if (self::$routeMap !== null) {
            return self::$routeMap;
        }

        $map = [];

        foreach (self::groups() as $group) {
            foreach ($group['tabs'] as $tab) {
                foreach ($tab['routes'] as $action => $routes) {
                    $permission = self::key($tab['key'], $action);
                    foreach ($routes as $route) {
                        if (isset($map[$route]) && $map[$route] !== $permission) {
                            $existing = is_array($map[$route]) ? $map[$route] : [$map[$route]];
                            $map[$route] = array_values(array_unique([...$existing, $permission]));

                            continue;
                        }

                        $map[$route] = $permission;
                    }
                }
            }
        }

        self::$routeMap = $map;

        return self::$routeMap;
    }
}

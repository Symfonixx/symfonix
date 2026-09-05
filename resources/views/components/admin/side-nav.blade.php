@props(['pendingTestimonialCount' => 0])

<div class="menu-section-label">{{ __('Overview') }}</div>

<div class="menu-item">
    <a class="menu-link {{ isset($active['dashboard']) ? 'active' : '' }}"
       href="{{ route('admin.dashboard.index') }}">
        <span class="menu-icon">
            <i class="bi bi-speedometer2"></i>
        </span>
        <span class="menu-title">{{ __('Dashboard') }}</span>
    </a>
</div>

@canany(['CRM Management', 'Sales Management'])
    <div class="menu-section-label">{{ __('Sales & CRM') }}</div>
@endcanany

@canany(['CRM Management', 'Sales Management'])
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['crm']) || isset($active['crm_dashboard']) || isset($active['crm_sales_targets']) || isset($active['marketing']) || isset($active['customers']) || isset($active['companies']) || isset($active['contacts']) || isset($active['deals']) || isset($active['subscriptions']) || isset($active['leads']) || isset($active['contact_forms']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-people"></i></span>
            <span class="menu-title">{{ __('CRM') }}</span>
            <span class="menu-arrow"></span>
        </span>

        <div class="menu-sub menu-sub-accordion {{ isset($active['crm_dashboard']) || isset($active['crm_sales_targets']) || isset($active['marketing']) || isset($active['customers']) || isset($active['companies']) || isset($active['contacts']) || isset($active['deals']) || isset($active['subscriptions']) || isset($active['leads']) || isset($active['contact_forms']) ? 'show' : '' }}">
            @can('CRM Management')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['crm_dashboard']) ? 'active' : '' }}"
                   href="{{ route('admin.crm.dashboard') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('crm::dashboard.menu') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['leads']) ? 'active' : '' }}"
                   href="{{ route('admin.leads.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('crm::lead.menu.leads') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['companies']) ? 'active' : '' }}"
                   href="{{ route('admin.companies.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('crm::company.menu.companies') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['contacts']) ? 'active' : '' }}"
                   href="{{ route('admin.contacts.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('crm::contact.menu.contacts') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['deals']) ? 'active' : '' }}"
                   href="{{ route('admin.deals.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('crm::deal.menu.deals') }}</span>
                </a>
            </div>
            @endcan
            @can('Sales Management')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['customers']) ? 'active' : '' }}"
                   href="{{ route('admin.customers.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Customers') }}</span>
                </a>
            </div>
            @endcan
            @can('CRM Management')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['subscriptions']) ? 'active' : '' }}"
                   href="{{ route('admin.subscriptions.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('crm::subscription.menu.subscriptions') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['contact_forms']) ? 'active' : '' }}"
                   href="{{ route('admin.contact_forms.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('crm::contact_form.menu.inquiries') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['marketing']) ? 'active' : '' }}"
                   href="{{ route('admin.crm.marketing.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('crm::marketing.menu.marketing') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['crm_sales_targets']) ? 'active' : '' }}"
                   href="{{ route('admin.crm.sales-targets.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('crm::sales_target.menu') }}</span>
                </a>
            </div>
            @endcan
        </div>
    </div>
@endcanany

@canany(['Project Management', 'Product Management'])
    <div class="menu-section-label">{{ __('Operations') }}</div>
@endcanany

@can('Project Management')
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['projects']) || isset($active['project_statuses']) || isset($active['project_use_cases']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-briefcase"></i></span>
            <span class="menu-title">{{ __('project::project.menu.projects') }}</span>
            <span class="menu-arrow"></span>
        </span>

        <div class="menu-sub menu-sub-accordion {{ isset($active['projects']) || isset($active['project_statuses']) || isset($active['project_use_cases']) ? 'show' : '' }}">
            <div class="menu-item">
                <a class="menu-link {{ isset($active['projects']) && !isset($active['project_statuses']) && !isset($active['project_use_cases']) ? 'active' : '' }}"
                   href="{{ route('admin.projects.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('project::project.menu.projects') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['project_use_cases']) ? 'active' : '' }}"
                   href="{{ route('admin.project-use-cases.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('project::use_case.menu.use_cases') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['project_statuses']) ? 'active' : '' }}"
                   href="{{ route('admin.project-statuses.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('project::project.menu.statuses') }}</span>
                </a>
            </div>
        </div>
    </div>
@endcan

@can('Product Management')
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['products']) || isset($active['product_categories']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-box-seam"></i></span>
            <span class="menu-title">{{ __('product::product.menu.products') }}</span>
            <span class="menu-arrow"></span>
        </span>

        <div class="menu-sub menu-sub-accordion {{ isset($active['products']) || isset($active['product_categories']) ? 'show' : '' }}">
            <div class="menu-item">
                <a class="menu-link {{ isset($active['products']) && !isset($active['product_categories']) ? 'active' : '' }}"
                   href="{{ route('admin.products.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('product::product.menu.catalog') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['product_categories']) ? 'active' : '' }}"
                   href="{{ route('admin.product-categories.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('product::category.menu') }}</span>
                </a>
            </div>
        </div>
    </div>
@endcan

@can('Finance Management')
    <div class="menu-section-label">{{ __('Finance') }}</div>
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['finance_dashboard']) || isset($active['finance_daily_log']) || isset($active['finance_expense_categories']) || isset($active['finance_salaries']) || isset($active['finance_commissions']) || isset($active['finance_product_sales']) || isset($active['finance_invoices']) || isset($active['finance_accounts_receivable']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-currency-dollar"></i></span>
            <span class="menu-title">{{ __('finance::finance.menu.finance') }}</span>
            <span class="menu-arrow"></span>
        </span>

        <div class="menu-sub menu-sub-accordion {{ isset($active['finance_dashboard']) || isset($active['finance_daily_log']) || isset($active['finance_expense_categories']) || isset($active['finance_salaries']) || isset($active['finance_commissions']) || isset($active['finance_product_sales']) || isset($active['finance_invoices']) || isset($active['finance_accounts_receivable']) ? 'show' : '' }}">
            <div class="menu-item">
                <a class="menu-link {{ isset($active['finance_dashboard']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.dashboard') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('finance::finance.menu.dashboard') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['finance_daily_log']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.daily-log') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('finance::finance.menu.daily_log') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['finance_invoices']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.invoices.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('finance::finance.menu.invoices') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['finance_accounts_receivable']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.accounts-receivable') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('finance::finance.menu.accounts_receivable') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['finance_product_sales']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.product-sales.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('finance::finance.menu.product_sales') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['finance_commissions']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.commissions.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('finance::finance.menu.commissions') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['finance_salaries']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.salaries.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('finance::finance.menu.salaries') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['finance_expense_categories']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.expense-categories.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('finance::finance.menu.expense_categories') }}</span>
                </a>
            </div>
        </div>
    </div>
@endcan

@can('Hr Management')
    <div class="menu-section-label">{{ __('People') }}</div>
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['hr']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-journal-text"></i></span>
            <span class="menu-title">{{ __('HR') }}</span>
            <span class="menu-arrow"></span>
        </span>

        <div
            class="menu-sub menu-sub-accordion {{ isset($active['roles']) || isset($active['admins']) || isset($active['employees']) || isset($active['leaves']) || isset($active['job_positions']) || isset($active['job_applications']) ? 'show' : '' }}">
            <div class="menu-item">
                <a class="menu-link {{ isset($active['employees']) ? 'active' : '' }}"
                   href="{{ route('admin.employees.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Employees') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['admins']) ? 'active' : '' }}"
                   href="{{ route('admin.admins.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Admins') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['roles']) ? 'active' : '' }}"
                   href="{{ route('admin.roles.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Roles') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['leaves']) ? 'active' : '' }}"
                   href="{{ route('admin.leaves.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Leave Management') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['job_positions']) ? 'active' : '' }}"
                   href="{{ route('admin.job-positions.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Job Positions') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['job_applications']) ? 'active' : '' }}"
                   href="{{ route('admin.job-applications.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Job Applications') }}</span>
                </a>
            </div>
        </div>
    </div>
@endcan

@canany(['CMS Management', 'Services Management', 'Testimonials Management', 'Team Management'])
    <div class="menu-section-label">{{ __('Content') }}</div>
@endcanany

@canany(['CMS Management', 'Testimonials Management', 'Team Management'])
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['cms']) || isset($active['pages']) || isset($active['blogs_categories']) || isset($active['blogs']) || isset($active['faqs']) || isset($active['clients']) || isset($active['slides']) || isset($active['filemanager']) || isset($active['testimonials']) || isset($active['teams']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-intersect"></i></span>
            <span class="menu-title">{{ __('CMS') }}</span>
            <span class="menu-arrow"></span>
        </span>

        <div
            class="menu-sub menu-sub-accordion {{ isset($active['faqs']) || isset($active['clients']) || isset($active['pages']) || isset($active['blogs_categories']) || isset($active['blogs']) || isset($active['slides']) || isset($active['filemanager']) || isset($active['testimonials']) || isset($active['teams']) ? 'show' : '' }}">
            @can('CMS Management')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['pages']) ? 'active' : '' }}"
                   href="{{ route('admin.pages.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Pages') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['blogs_categories']) ? 'active' : '' }}"
                   href="{{ route('admin.blogs_categories.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Blog Categories') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['blogs']) ? 'active' : '' }}"
                   href="{{ route('admin.blogs.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Blogs') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['faqs']) ? 'active' : '' }}"
                   href="{{ route('admin.faqs.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('FAQs') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['clients']) ? 'active' : '' }}"
                   href="{{ route('admin.clients.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Our Clients') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['filemanager']) ? 'active' : '' }}"
                   href="{{ route('admin.filemanager.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('File Manager') }}</span>
                </a>
            </div>
            @endcan
            @can('Testimonials Management')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['testimonials']) ? 'active' : '' }}"
                   href="{{ route('admin.testimonials.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Testimonials') }}</span>
                    @if($pendingTestimonialCount > 0)
                        <span class="badge badge-circle badge-danger ms-auto">{{ $pendingTestimonialCount }}</span>
                    @endif
                </a>
            </div>
            @endcan
            @can('Team Management')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['teams']) ? 'active' : '' }}"
                   href="{{ route('admin.teams.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Our Team') }}</span>
                </a>
            </div>
            @endcan
        </div>
    </div>
@endcanany

@can('Services Management')
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['services']) || isset($active['service_categories']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-grid"></i></span>
            <span class="menu-title">{{ __('Services') }}</span>
            <span class="menu-arrow"></span>
        </span>

        <div class="menu-sub menu-sub-accordion {{ isset($active['services']) || isset($active['service_categories']) ? 'show' : '' }}">
            <div class="menu-item">
                <a class="menu-link {{ isset($active['services']) && !isset($active['service_categories']) ? 'active' : '' }}"
                   href="{{ route('admin.services.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Services') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['service_categories']) ? 'active' : '' }}"
                   href="{{ route('admin.service_categories.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Service Categories') }}</span>
                </a>
            </div>
        </div>
    </div>
@endcan

@canany(['Settings Management', 'Support Management', 'Logs Management'])
    <div class="menu-section-label">{{ __('System') }}</div>
@endcanany

@can('Settings Management')
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['settings']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-gear"></i></span>
            <span class="menu-title">{{ __('Settings') }}</span>
            <span class="menu-arrow"></span>
        </span>

        <div
            class="menu-sub menu-sub-accordion {{ isset($active['websiteConfigurations']) || isset($active['systemConfigurations']) || isset($active['seo']) ? 'show' : '' }}">
            <div class="menu-item">
                <a class="menu-link {{ isset($active['websiteConfigurations']) ? 'active' : '' }}"
                   href="{{ route('admin.settings.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Website Configurations') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['systemConfigurations']) ? 'active' : '' }}"
                   href="{{ route('admin.system-configurations.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('base::system.title') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['seo']) ? 'active' : '' }}"
                   href="{{ route('admin.seo.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Seo Configurations') }}</span>
                </a>
            </div>
        </div>
    </div>
@endcan

@can('Support Management')
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['support']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-headset"></i></span>
            <span class="menu-title">{{ __('Support Hub') }}</span>
            <span class="menu-arrow"></span>
        </span>

        <div
            class="menu-sub menu-sub-accordion {{ isset($active['subscribers']) || isset($active['search_keywords']) || isset($active['complaints']) || isset($active['visitors']) || isset($active['tickets']) || isset($active['ticket_categories']) ? 'show' : '' }}">
            <div class="menu-item">
                <a class="menu-link {{ isset($active['tickets']) ? 'active' : '' }}"
                   href="{{ route('admin.tickets.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('support::ticket.menu.tickets') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['ticket_categories']) ? 'active' : '' }}"
                   href="{{ route('admin.ticket_categories.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('support::ticket.menu.categories') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['visitors']) ? 'active' : '' }}"
                   href="{{ route('admin.visitors.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Visitors') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['subscribers']) ? 'active' : '' }}"
                   href="{{ route('admin.subscribers.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Newsletter Subscribers') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['search_keywords']) ? 'active' : '' }}"
                   href="{{ route('admin.search_keywords.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Search Keywords') }}</span>
                </a>
            </div>
        </div>
    </div>
@endcan

@can('Settings Management')
    <div class="menu-item">
        <a class="menu-link {{ isset($active['backups']) ? 'active' : '' }}"
           href="{{ route('admin.backups.index') }}">
            <span class="menu-icon"><i class="bi bi-database-down"></i></span>
            <span class="menu-title">{{ __('base::backup.title') }}</span>
        </a>
    </div>
@endcan

@can('Logs Management')
    <div class="menu-item">
        <a class="menu-link {{ isset($active['logs']) ? 'active' : '' }}"
           href="{{ route('admin.logs.index') }}">
            <span class="menu-icon"><i class="bi bi-window-stack"></i></span>
            <span class="menu-title">{{ __('Logs & Bugs') }}</span>
        </a>
    </div>
@endcan

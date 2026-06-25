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

@canany(['Settings Management', 'CMS Management', 'Services Management'])
    <div class="menu-section-label">{{ __('Content') }}</div>
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
            class="menu-sub menu-sub-accordion {{ isset($active['websiteConfigurations']) || isset($active['seo']) ? 'show' : '' }}">
            <div class="menu-item">
                <a class="menu-link {{ isset($active['websiteConfigurations']) ? 'active' : '' }}"
                   href="{{ route('admin.settings.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Website Configurations') }}</span>
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

@can('CMS Management')
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['cms']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-intersect"></i></span>
            <span class="menu-title">{{ __('CMS') }}</span>
            <span class="menu-arrow"></span>
        </span>

        <div
            class="menu-sub menu-sub-accordion {{ isset($active['faqs']) || isset($active['pages']) || isset($active['blogs_categories']) || isset($active['blogs']) || isset($active['slides']) || isset($active['filemanager']) ? 'show' : '' }}">
            <div class="menu-item">
                <a class="menu-link {{ isset($active['faqs']) ? 'active' : '' }}"
                   href="{{ route('admin.faqs.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('FAQs') }}</span>
                </a>
            </div>
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
                <a class="menu-link {{ isset($active['filemanager']) ? 'active' : '' }}"
                   href="{{ route('admin.filemanager.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('File Manager') }}</span>
                </a>
            </div>
        </div>
    </div>
@endcan

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

@canany(['Hr Management', 'CRM Management', 'Sales Management'])
    <div class="menu-section-label">{{ __('People') }}</div>
@endcanany

@can('Hr Management')
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['hr']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-journal-text"></i></span>
            <span class="menu-title">{{ __('HR') }}</span>
            <span class="menu-arrow"></span>
        </span>

        <div
            class="menu-sub menu-sub-accordion {{ isset($active['roles']) || isset($active['admins']) || isset($active['employees']) ? 'show' : '' }}">
            <div class="menu-item">
                <a class="menu-link {{ isset($active['roles']) ? 'active' : '' }}"
                   href="{{ route('admin.roles.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Roles') }}</span>
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
                <a class="menu-link {{ isset($active['employees']) ? 'active' : '' }}"
                   href="{{ route('admin.employees.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Employees') }}</span>
                </a>
            </div>
        </div>
    </div>
@endcan

@canany(['CRM Management', 'Sales Management'])
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['crm']) || isset($active['crm_dashboard']) || isset($active['crm_sales_targets']) || isset($active['customers']) || isset($active['companies']) || isset($active['deals']) || isset($active['subscriptions']) || isset($active['leads']) || isset($active['contact_forms']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-people"></i></span>
            <span class="menu-title">{{ __('CRM') }}</span>
            <span class="menu-arrow"></span>
        </span>

        <div class="menu-sub menu-sub-accordion {{ isset($active['crm_dashboard']) || isset($active['crm_sales_targets']) || isset($active['customers']) || isset($active['companies']) || isset($active['deals']) || isset($active['subscriptions']) || isset($active['leads']) || isset($active['contact_forms']) ? 'show' : '' }}">
            @can('CRM Management')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['crm_dashboard']) ? 'active' : '' }}"
                   href="{{ route('admin.crm.dashboard') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('crm::dashboard.menu') }}</span>
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
                <a class="menu-link {{ isset($active['companies']) ? 'active' : '' }}"
                   href="{{ route('admin.companies.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('crm::company.menu.companies') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['deals']) ? 'active' : '' }}"
                   href="{{ route('admin.deals.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('crm::deal.menu.deals') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['subscriptions']) ? 'active' : '' }}"
                   href="{{ route('admin.subscriptions.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('crm::subscription.menu.subscriptions') }}</span>
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
                <a class="menu-link {{ isset($active['contact_forms']) ? 'active' : '' }}"
                   href="{{ route('admin.contact_forms.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('crm::contact.menu.contacts') }}</span>
                </a>
            </div>
            @endcan
        </div>
    </div>
@endcanany

@can('Project Management')
    <div class="menu-section-label">{{ __('Operations') }}</div>
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
    <div class="menu-section-label">{{ __('product::product.menu.products') }}</div>
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
         class="menu-item menu-accordion {{ isset($active['finance_dashboard']) || isset($active['finance_daily_log']) || isset($active['finance_expense_categories']) || isset($active['finance_salaries']) || isset($active['finance_commissions']) || isset($active['finance_product_sales']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-currency-dollar"></i></span>
            <span class="menu-title">{{ __('finance::finance.menu.finance') }}</span>
            <span class="menu-arrow"></span>
        </span>

        <div class="menu-sub menu-sub-accordion {{ isset($active['finance_dashboard']) || isset($active['finance_daily_log']) || isset($active['finance_expense_categories']) || isset($active['finance_salaries']) || isset($active['finance_commissions']) || isset($active['finance_product_sales']) ? 'show' : '' }}">
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
                <a class="menu-link {{ isset($active['finance_expense_categories']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.expense-categories.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('finance::finance.menu.expense_categories') }}</span>
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
                <a class="menu-link {{ isset($active['finance_commissions']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.commissions.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('finance::finance.menu.commissions') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['finance_product_sales']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.product-sales.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('finance::finance.menu.product_sales') }}</span>
                </a>
            </div>
        </div>
    </div>
@endcan

@canany(['Support Management', 'Logs Management'])
    <div class="menu-section-label">{{ __('System') }}</div>
@endcanany

@can('Support Management')
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['support']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-headset"></i></span>
            <span class="menu-title">{{ __('Support Hub') }}</span>
            <span class="menu-arrow"></span>
        </span>

        <div
            class="menu-sub menu-sub-accordion {{ isset($active['subscribers']) || isset($active['search_keywords']) || isset($active['complaints']) || isset($active['visitors']) ? 'show' : '' }}">
            <div class="menu-item">
                <a class="menu-link {{ isset($active['subscribers']) ? 'active' : '' }}"
                   href="{{ route('admin.subscribers.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Newsletter Subscribers') }}</span>
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
                <a class="menu-link {{ isset($active['search_keywords']) ? 'active' : '' }}"
                   href="{{ route('admin.search_keywords.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Search Keywords') }}</span>
                </a>
            </div>
        </div>
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

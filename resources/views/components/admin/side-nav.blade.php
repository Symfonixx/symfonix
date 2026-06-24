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

@canany(['Settings Management', 'CMS Management'])
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
            class="menu-sub menu-sub-accordion {{ isset($active['faqs']) || isset($active['pages']) || isset($active['blogs_categories']) || isset($active['blogs']) || isset($active['slides']) || isset($active['service_categories']) || isset($active['services']) || isset($active['filemanager']) ? 'show' : '' }}">
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
                <a class="menu-link {{ isset($active['service_categories']) ? 'active' : '' }}"
                   href="{{ route('admin.service_categories.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Service Categories') }}</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ isset($active['services']) ? 'active' : '' }}"
                   href="{{ route('admin.services.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Services') }}</span>
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

@canany(['Hr Management', 'CRM Management'])
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

@can('CRM Management')
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['crm']) || isset($active['crm_dashboard']) || isset($active['crm_sales_targets']) || isset($active['customers']) || isset($active['companies']) || isset($active['deals']) || isset($active['subscriptions']) || isset($active['leads']) || isset($active['contact_forms']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-people"></i></span>
            <span class="menu-title">{{ __('CRM') }}</span>
            <span class="menu-arrow"></span>
        </span>

        <div class="menu-sub menu-sub-accordion {{ isset($active['crm_dashboard']) || isset($active['crm_sales_targets']) || isset($active['customers']) || isset($active['companies']) || isset($active['deals']) || isset($active['subscriptions']) || isset($active['leads']) || isset($active['contact_forms']) ? 'show' : '' }}">
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
            <div class="menu-item">
                <a class="menu-link {{ isset($active['customers']) ? 'active' : '' }}"
                   href="{{ route('admin.customers.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">{{ __('Customers') }}</span>
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

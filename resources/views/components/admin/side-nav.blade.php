@props(['pendingTestimonialCount' => 0, 'navCounts' => []])

@php
    $navCounts = array_merge([
        'open_deals' => 0,
        'new_leads' => 0,
        'pending_tasks' => 0,
        'open_tickets' => 0,
        'pending_quotes' => 0,
        'pending_inquiries' => 0,
        'open_invoices' => 0,
    ], $navCounts);

    $overviewHere = isset($active['dashboard']) || isset($active['crm_dashboard']);
    $relationshipsHere = isset($active['leads']) || isset($active['contacts']) || isset($active['companies']) || isset($active['customers']);
    $salesHere = isset($active['deals']) || isset($active['pipeline']) || isset($active['quotes']) || isset($active['subscriptions']) || isset($active['marketing']) || isset($active['sales_forecasts']) || isset($active['finance_invoices']);
    $serviceHere = isset($active['tickets']) || isset($active['ticket_categories']) || isset($active['crm_calendar']) || isset($active['contact_forms']);
    $crmSettingsHere = isset($active['crm_settings']) || isset($active['crm_sales_targets']) || isset($active['crm_lead_tags']) || isset($active['crm_custom_fields']);
    $websiteSettingsHere = isset($active['websiteConfigurations']) || isset($active['systemConfigurations']) || isset($active['integrations']) || isset($active['seo']);
    $supportExtrasHere = isset($active['subscribers']) || isset($active['search_keywords']) || isset($active['visitors']);
    $systemHere = $crmSettingsHere || $websiteSettingsHere || $supportExtrasHere || isset($active['backups']) || isset($active['logs']) || isset($active['settings']);
    $reportingHere = isset($active['reporting_finance']) || isset($active['reporting_sales']) || isset($active['reporting_marketing']) || isset($active['reporting_operations']) || isset($active['reporting_employee']);
@endphp

<div class="menu-section-label">{{ __('Overview') }}</div>
<div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ $overviewHere ? 'here show' : '' }}">
    <span class="menu-link">
        <span class="menu-icon"><i class="bi bi-grid-1x2-fill text-primary"></i></span>
        <span class="menu-title">{{ __('Overview') }}</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion {{ $overviewHere ? 'show' : '' }}">
        <div class="menu-item">
            <a class="menu-link {{ isset($active['dashboard']) ? 'active' : '' }}"
               href="{{ route('admin.dashboard.index') }}">
                <span class="menu-icon menu-icon-sm"><i class="bi bi-speedometer2 text-primary"></i></span>
                <span class="menu-title">{{ __('Dashboard') }}</span>
            </a>
        </div>
        @canTab('overview.crm_analytics')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['crm_dashboard']) ? 'active' : '' }}"
                   href="{{ route('admin.crm.dashboard') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-graph-up-arrow text-info"></i></span>
                    <span class="menu-title">{{ __('CRM Analytics') }}</span>
                </a>
            </div>
        @endcanTab
    </div>
</div>

@canSection('cms')
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['cms']) || isset($active['pages']) || isset($active['blogs_categories']) || isset($active['blogs']) || isset($active['faqs']) || isset($active['clients']) || isset($active['slides']) || isset($active['filemanager']) || isset($active['testimonials']) || isset($active['teams']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-layers-fill text-primary"></i></span>
            <span class="menu-title">{{ __('CMS') }}</span>
            @if($pendingTestimonialCount > 0)
                <x-admin.nav-badge :count="$pendingTestimonialCount" color="danger"/>
            @endif
            <span class="menu-arrow"></span>
        </span>
        <div class="menu-sub menu-sub-accordion {{ isset($active['faqs']) || isset($active['clients']) || isset($active['pages']) || isset($active['blogs_categories']) || isset($active['blogs']) || isset($active['slides']) || isset($active['filemanager']) || isset($active['testimonials']) || isset($active['teams']) ? 'show' : '' }}">
            @canTab('cms.pages')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['pages']) ? 'active' : '' }}"
                       href="{{ route('admin.pages.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-file-earmark text-primary"></i></span>
                        <span class="menu-title">{{ __('Pages') }}</span>
                    </a>
                </div>
            @endcanTab
            @canTab('cms.blog_categories')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['blogs_categories']) ? 'active' : '' }}"
                       href="{{ route('admin.blogs_categories.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-folder2 text-info"></i></span>
                        <span class="menu-title">{{ __('Blog Categories') }}</span>
                    </a>
                </div>
            @endcanTab
            @canTab('cms.blogs')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['blogs']) ? 'active' : '' }}"
                       href="{{ route('admin.blogs.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-pencil-square text-success"></i></span>
                        <span class="menu-title">{{ __('Blogs') }}</span>
                    </a>
                </div>
            @endcanTab
            @canTab('cms.faqs')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['faqs']) ? 'active' : '' }}"
                       href="{{ route('admin.faqs.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-question-circle text-warning"></i></span>
                        <span class="menu-title">{{ __('FAQs') }}</span>
                    </a>
                </div>
            @endcanTab
            @canTab('cms.clients')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['clients']) ? 'active' : '' }}"
                       href="{{ route('admin.clients.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-award text-primary"></i></span>
                        <span class="menu-title">{{ __('Our Clients') }}</span>
                    </a>
                </div>
            @endcanTab
            @canTab('cms.file_manager')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['filemanager']) ? 'active' : '' }}"
                       href="{{ route('admin.filemanager.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-folder-symlink text-info"></i></span>
                        <span class="menu-title">{{ __('File Manager') }}</span>
                    </a>
                </div>
            @endcanTab
            @canTab('cms.testimonials')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['testimonials']) ? 'active' : '' }}"
                       href="{{ route('admin.testimonials.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-chat-quote text-warning"></i></span>
                        <span class="menu-title">{{ __('Testimonials') }}</span>
                        <x-admin.nav-badge :count="$pendingTestimonialCount" color="danger"/>
                    </a>
                </div>
            @endcanTab
            @canTab('cms.team')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['teams']) ? 'active' : '' }}"
                       href="{{ route('admin.teams.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-person-bounding-box text-success"></i></span>
                        <span class="menu-title">{{ __('Our Team') }}</span>
                    </a>
                </div>
            @endcanTab
        </div>
    </div>
@endcanSection

@canSection('services')
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['services']) || isset($active['service_categories']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-grid-3x3-gap-fill text-success"></i></span>
            <span class="menu-title">{{ __('Services') }}</span>
            <span class="menu-arrow"></span>
        </span>
        <div class="menu-sub menu-sub-accordion {{ isset($active['services']) || isset($active['service_categories']) ? 'show' : '' }}">
            @canTab('services.catalog')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['services']) && !isset($active['service_categories']) ? 'active' : '' }}"
                   href="{{ route('admin.services.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-grid text-success"></i></span>
                    <span class="menu-title">{{ __('Services') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('services.categories')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['service_categories']) ? 'active' : '' }}"
                   href="{{ route('admin.service_categories.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-collection text-info"></i></span>
                    <span class="menu-title">{{ __('Service Categories') }}</span>
                </a>
            </div>
            @endcanTab
        </div>
    </div>
@endcanSection


@canSection('crm')
    <div class="menu-section-label">{{ __('Relationship Management') }}</div>
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ $relationshipsHere ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-people-fill text-info"></i></span>
            <span class="menu-title">{{ __('Relationship Management') }}</span>
            <span class="menu-arrow"></span>
        </span>
        <div class="menu-sub menu-sub-accordion {{ $relationshipsHere ? 'show' : '' }}">
            @canTab('crm.leads')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['leads']) ? 'active' : '' }}"
                       href="{{ route('admin.leads.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-person-lines-fill text-info"></i></span>
                        <span class="menu-title">{{ __('crm::lead.menu.leads') }}</span>
                        <x-admin.nav-badge :count="$navCounts['new_leads']" color="info"/>
                    </a>
                </div>
            @endcanTab
            @canTab('crm.contacts')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['contacts']) ? 'active' : '' }}"
                       href="{{ route('admin.contacts.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-person-badge text-primary"></i></span>
                        <span class="menu-title">{{ __('crm::contact.menu.contacts') }}</span>
                    </a>
                </div>
            @endcanTab
            @canTab('crm.companies')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['companies']) ? 'active' : '' }}"
                       href="{{ route('admin.companies.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-building text-warning"></i></span>
                        <span class="menu-title">{{ __('crm::company.menu.companies') }}</span>
                    </a>
                </div>
            @endcanTab
            @canTab('sales.customers')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['customers']) ? 'active' : '' }}"
                       href="{{ route('admin.customers.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-people text-success"></i></span>
                        <span class="menu-title">{{ __('Customers') }}</span>
                    </a>
                </div>
            @endcanTab
        </div>
    </div>
@endcanSection

@canSection('sales')
    <div class="menu-section-label">{{ __('Sales & Deals') }}</div>
    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ $salesHere ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-kanban-fill text-success"></i></span>
            <span class="menu-title">{{ __('Sales & Deals') }}</span>
            @if(($navCounts['open_deals'] + $navCounts['pending_quotes'] + $navCounts['open_invoices']) > 0)
                <x-admin.nav-badge :count="$navCounts['open_deals'] + $navCounts['pending_quotes'] + $navCounts['open_invoices']" color="warning"/>
            @endif
            <span class="menu-arrow"></span>
        </span>
        <div class="menu-sub menu-sub-accordion {{ $salesHere ? 'show' : '' }}">
            @canTab('sales.pipeline')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['pipeline']) ? 'active' : '' }}"
                       href="{{ route('admin.deals.index', ['view' => 'kanban']) }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-columns-gap text-success"></i></span>
                        <span class="menu-title">{{ __('crm::deal.menu.pipeline') }}</span>
                    </a>
                </div>
            @endcanTab
            @canTab('sales.deals')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['deals']) && ! isset($active['pipeline']) ? 'active' : '' }}"
                       href="{{ route('admin.deals.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-briefcase-fill text-primary"></i></span>
                        <span class="menu-title">{{ __('crm::deal.menu.deals') }}</span>
                        <x-admin.nav-badge :count="$navCounts['open_deals']" color="warning"/>
                    </a>
                </div>
            @endcanTab
            @canTab('sales.quotes')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['quotes']) ? 'active' : '' }}"
                       href="{{ route('admin.quotes.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-file-earmark-text text-info"></i></span>
                        <span class="menu-title">{{ __('crm::quote.menu.quotes') }}</span>
                        <x-admin.nav-badge :count="$navCounts['pending_quotes']" color="info"/>
                    </a>
                </div>
            @endcanTab
            @canTab('finance.invoices')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['finance_invoices']) ? 'active' : '' }}"
                       href="{{ route('admin.finance.invoices.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-receipt text-danger"></i></span>
                        <span class="menu-title">{{ __('finance::finance.menu.invoices') }}</span>
                        <x-admin.nav-badge :count="$navCounts['open_invoices']" color="danger"/>
                    </a>
                </div>
            @endcanTab
            @canTab('sales.subscriptions')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['subscriptions']) ? 'active' : '' }}"
                       href="{{ route('admin.subscriptions.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-arrow-repeat text-warning"></i></span>
                        <span class="menu-title">{{ __('crm::subscription.menu.subscriptions') }}</span>
                    </a>
                </div>
            @endcanTab
            @canTab(['marketing.email', 'marketing.whatsapp', 'marketing.whatsapp_templates'])
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['marketing']) ? 'active' : '' }}"
                       href="{{ route('admin.crm.marketing.index', auth()->user()?->can('marketing.email.view') ? ['channel' => 'email'] : ['channel' => 'whatsapp']) }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-megaphone text-primary"></i></span>
                        <span class="menu-title">{{ __('crm::marketing.menu.marketing') }}</span>
                    </a>
                </div>
            @endcanTab
            @canTab('sales.forecasts')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['sales_forecasts']) ? 'active' : '' }}"
                       href="{{ route('admin.crm.sales-forecasts.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-graph-up-arrow text-info"></i></span>
                        <span class="menu-title">{{ __('crm::forecast.menu') }}</span>
                    </a>
                </div>
            @endcanTab
        </div>
    </div>
@endcanSection

@if(auth()->user()?->canany(array_merge(permission_section_keys('project'), permission_section_keys('product'))))
    <div class="menu-section-label">{{ __('Operations') }}</div>
@endif

@canSection('project')
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['projects']) || isset($active['project_statuses']) || isset($active['project_use_cases']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-kanban text-primary"></i></span>
            <span class="menu-title">{{ __('project::project.menu.projects') }}</span>
            <span class="menu-arrow"></span>
        </span>
        <div class="menu-sub menu-sub-accordion {{ isset($active['projects']) || isset($active['project_statuses']) || isset($active['project_use_cases']) ? 'show' : '' }}">
            @canTab('project.projects')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['projects']) && !isset($active['project_statuses']) && !isset($active['project_use_cases']) ? 'active' : '' }}"
                   href="{{ route('admin.projects.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-briefcase text-primary"></i></span>
                    <span class="menu-title">{{ __('project::project.menu.projects') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('project.use_cases')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['project_use_cases']) ? 'active' : '' }}"
                   href="{{ route('admin.project-use-cases.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-lightbulb text-warning"></i></span>
                    <span class="menu-title">{{ __('project::use_case.menu.use_cases') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('project.statuses')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['project_statuses']) ? 'active' : '' }}"
                   href="{{ route('admin.project-statuses.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-flag text-info"></i></span>
                    <span class="menu-title">{{ __('project::project.menu.statuses') }}</span>
                </a>
            </div>
            @endcanTab
        </div>
    </div>
@endcanSection

@canSection('product')
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['products']) || isset($active['product_categories']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-box-seam-fill text-warning"></i></span>
            <span class="menu-title">{{ __('product::product.menu.products') }}</span>
            <span class="menu-arrow"></span>
        </span>
        <div class="menu-sub menu-sub-accordion {{ isset($active['products']) || isset($active['product_categories']) ? 'show' : '' }}">
            @canTab('product.catalog')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['products']) && !isset($active['product_categories']) ? 'active' : '' }}"
                   href="{{ route('admin.products.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-box-seam text-warning"></i></span>
                    <span class="menu-title">{{ __('product::product.menu.catalog') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('product.categories')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['product_categories']) ? 'active' : '' }}"
                   href="{{ route('admin.product-categories.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-grid text-primary"></i></span>
                    <span class="menu-title">{{ __('product::category.menu') }}</span>
                </a>
            </div>
            @endcanTab
        </div>
    </div>
@endcanSection

@if(auth()->user()?->canany(array_merge(permission_section_keys('finance'), permission_section_keys('tax'))))
    <div class="menu-section-label">{{ __('Finance') }}</div>
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['finance_dashboard']) || isset($active['finance_daily_log']) || isset($active['finance_expense_categories']) || isset($active['finance_salaries']) || isset($active['finance_commissions']) || isset($active['finance_product_sales']) || isset($active['finance_accounts_receivable']) || isset($active['tax_rates']) || isset($active['tax_ledger']) || isset($active['tax_reports']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-cash-stack text-success"></i></span>
            <span class="menu-title">{{ __('finance::finance.menu.finance') }}</span>
            <span class="menu-arrow"></span>
        </span>
        <div class="menu-sub menu-sub-accordion {{ isset($active['finance_dashboard']) || isset($active['finance_daily_log']) || isset($active['finance_expense_categories']) || isset($active['finance_salaries']) || isset($active['finance_commissions']) || isset($active['finance_product_sales']) || isset($active['finance_accounts_receivable']) || isset($active['tax_rates']) || isset($active['tax_ledger']) || isset($active['tax_reports']) ? 'show' : '' }}">
            @canTab('finance.dashboard')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['finance_dashboard']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.dashboard') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-pie-chart text-success"></i></span>
                    <span class="menu-title">{{ __('finance::finance.menu.dashboard') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('finance.daily_log')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['finance_daily_log']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.daily-log') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-journal-text text-info"></i></span>
                    <span class="menu-title">{{ __('finance::finance.menu.daily_log') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('finance.ar')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['finance_accounts_receivable']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.accounts-receivable') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-wallet2 text-warning"></i></span>
                    <span class="menu-title">{{ __('finance::finance.menu.accounts_receivable') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('finance.product_sales')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['finance_product_sales']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.product-sales.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-cart-check text-primary"></i></span>
                    <span class="menu-title">{{ __('finance::finance.menu.product_sales') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('finance.commissions')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['finance_commissions']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.commissions.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-percent text-success"></i></span>
                    <span class="menu-title">{{ __('finance::finance.menu.commissions') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('finance.salaries')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['finance_salaries']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.salaries.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-person-badge text-info"></i></span>
                    <span class="menu-title">{{ __('finance::finance.menu.salaries') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('finance.expense_categories')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['finance_expense_categories']) ? 'active' : '' }}"
                   href="{{ route('admin.finance.expense-categories.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-folder2-open text-danger"></i></span>
                    <span class="menu-title">{{ __('finance::finance.menu.expense_categories') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('tax.rates')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['tax_rates']) ? 'active' : '' }}"
                   href="{{ route('admin.tax.rates.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-percent text-warning"></i></span>
                    <span class="menu-title">{{ __('tax::tax.menu.tax_rates') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('tax.ledger')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['tax_ledger']) ? 'active' : '' }}"
                   href="{{ route('admin.tax.ledger.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-journal-bookmark text-info"></i></span>
                    <span class="menu-title">{{ __('tax::tax.menu.ledger') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('tax.filing')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['tax_reports']) ? 'active' : '' }}"
                   href="{{ route('admin.tax.reports.filing') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-file-earmark-bar-graph text-success"></i></span>
                    <span class="menu-title">{{ __('tax::tax.menu.filing_report') }}</span>
                </a>
            </div>
            @endcanTab
        </div>
    </div>
@endif



@canSection('hr')
    <div class="menu-section-label">{{ __('Team Management') }}</div>
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ isset($active['hr']) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-people text-info"></i></span>
            <span class="menu-title">{{ __('HR') }}</span>
            <span class="menu-arrow"></span>
        </span>
        <div class="menu-sub menu-sub-accordion {{ isset($active['roles']) || isset($active['admins']) || isset($active['employees']) || isset($active['fingerprint']) || isset($active['leaves']) || isset($active['job_positions']) || isset($active['job_applications']) ? 'show' : '' }}">
            @canTab('hr.employees')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['employees']) ? 'active' : '' }}"
                   href="{{ route('admin.employees.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-person-badge text-primary"></i></span>
                    <span class="menu-title">{{ __('Employees') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('hr.fingerprint')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['fingerprint']) ? 'active' : '' }}"
                   href="{{ route('admin.fingerprint.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-fingerprint text-info"></i></span>
                    <span class="menu-title">{{ __('user::fingerprint.title') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('hr.admins')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['admins']) ? 'active' : '' }}"
                   href="{{ route('admin.admins.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-shield-lock text-warning"></i></span>
                    <span class="menu-title">{{ __('Admins') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('hr.roles')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['roles']) ? 'active' : '' }}"
                   href="{{ route('admin.roles.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-key text-info"></i></span>
                    <span class="menu-title">{{ __('Roles') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('hr.leaves')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['leaves']) ? 'active' : '' }}"
                   href="{{ route('admin.leaves.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-calendar2-week text-success"></i></span>
                    <span class="menu-title">{{ __('Leave Management') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('hr.job_positions')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['job_positions']) ? 'active' : '' }}"
                   href="{{ route('admin.job-positions.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-briefcase text-primary"></i></span>
                    <span class="menu-title">{{ __('Job Positions') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('hr.job_applications')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['job_applications']) ? 'active' : '' }}"
                   href="{{ route('admin.job-applications.index') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-file-earmark-person text-danger"></i></span>
                    <span class="menu-title">{{ __('Job Applications') }}</span>
                </a>
            </div>
            @endcanTab
        </div>
    </div>
@endcanSection

@canSection('support')
    <div class="menu-section-label">{{ __('Customer Service & Support') }}</div>
    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ $serviceHere ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-headset text-warning"></i></span>
            <span class="menu-title">{{ __('Customer Service & Support') }}</span>
            @if(($navCounts['open_tickets'] + $navCounts['pending_tasks'] + $navCounts['pending_inquiries']) > 0)
                <x-admin.nav-badge :count="$navCounts['open_tickets'] + $navCounts['pending_tasks'] + $navCounts['pending_inquiries']" color="danger"/>
            @endif
            <span class="menu-arrow"></span>
        </span>
        <div class="menu-sub menu-sub-accordion {{ $serviceHere ? 'show' : '' }}">
            @canTab('support.tickets')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['tickets']) ? 'active' : '' }}"
                       href="{{ route('admin.tickets.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-ticket-detailed text-warning"></i></span>
                        <span class="menu-title">{{ __('support::ticket.menu.tickets') }}</span>
                        <x-admin.nav-badge :count="$navCounts['open_tickets']" color="warning"/>
                    </a>
                </div>
            @endcanTab
            @canTab('support.ticket_categories')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['ticket_categories']) ? 'active' : '' }}"
                       href="{{ route('admin.ticket_categories.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-tags text-secondary"></i></span>
                        <span class="menu-title">{{ __('support::ticket.menu.categories') }}</span>
                    </a>
                </div>
            @endcanTab
            @canTab('crm.activities')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['crm_calendar']) ? 'active' : '' }}"
                       href="{{ route('admin.crm.calendar') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-calendar-check text-success"></i></span>
                        <span class="menu-title">{{ __('Activities') }}</span>
                        <x-admin.nav-badge :count="$navCounts['pending_tasks']" color="success"/>
                    </a>
                </div>
            @endcanTab
            @canTab('crm.inquiries')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['contact_forms']) ? 'active' : '' }}"
                       href="{{ route('admin.contact_forms.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-inbox text-danger"></i></span>
                        <span class="menu-title">{{ __('crm::contact_form.menu.inquiries') }}</span>
                        <x-admin.nav-badge :count="$navCounts['pending_inquiries']" color="danger"/>
                    </a>
                </div>
            @endcanTab
        </div>
    </div>
@endcanSection

@canSection('reporting')
    <div class="menu-section-label">{{ __('reporting::report.menu.reports') }}</div>
    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ $reportingHere ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-file-earmark-bar-graph text-primary"></i></span>
            <span class="menu-title">{{ __('reporting::report.menu.reports') }}</span>
            <span class="menu-arrow"></span>
        </span>
        <div class="menu-sub menu-sub-accordion {{ $reportingHere ? 'show' : '' }}">
            @canTab('reporting.finance')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['reporting_finance']) ? 'active' : '' }}"
                   href="{{ route('admin.reporting.finance') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-currency-dollar text-success"></i></span>
                    <span class="menu-title">{{ __('reporting::report.menu.finance') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('reporting.sales')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['reporting_sales']) ? 'active' : '' }}"
                   href="{{ route('admin.reporting.sales') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-graph-up-arrow text-info"></i></span>
                    <span class="menu-title">{{ __('reporting::report.menu.sales') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('reporting.marketing')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['reporting_marketing']) ? 'active' : '' }}"
                   href="{{ route('admin.reporting.marketing') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-megaphone text-warning"></i></span>
                    <span class="menu-title">{{ __('reporting::report.menu.marketing') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('reporting.operations')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['reporting_operations']) ? 'active' : '' }}"
                   href="{{ route('admin.reporting.operations') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-gear-wide-connected text-danger"></i></span>
                    <span class="menu-title">{{ __('reporting::report.menu.operations') }}</span>
                </a>
            </div>
            @endcanTab
            @canTab('reporting.employee')
            <div class="menu-item">
                <a class="menu-link {{ isset($active['reporting_employee']) ? 'active' : '' }}"
                   href="{{ route('admin.reporting.employee') }}">
                    <span class="menu-icon menu-icon-sm"><i class="bi bi-person-badge text-primary"></i></span>
                    <span class="menu-title">{{ __('reporting::report.menu.employee') }}</span>
                </a>
            </div>
            @endcanTab
        </div>
    </div>
@endcanSection

@canSection('settings')
    <div class="menu-section-label">{{ __('System & Settings') }}</div>
    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ $systemHere ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon"><i class="bi bi-gear-fill text-secondary"></i></span>
            <span class="menu-title">{{ __('System & Settings') }}</span>
            <span class="menu-arrow"></span>
        </span>
        <div class="menu-sub menu-sub-accordion {{ $systemHere ? 'show' : '' }}">
            @if(auth()->user()?->canany(permission_keys(['crm.sales_targets', 'crm.lead_tags', 'crm.custom_fields'])))
                <div data-kt-menu-trigger="click"
                     class="menu-item menu-accordion {{ $crmSettingsHere ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-sliders text-primary"></i></span>
                        <span class="menu-title">{{ __('crm::settings.menu') }}</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion {{ $crmSettingsHere ? 'show' : '' }}">
                        @canTab('crm.sales_targets')
                        <div class="menu-item">
                            <a class="menu-link {{ isset($active['crm_sales_targets']) ? 'active' : '' }}"
                               href="{{ route('admin.crm.sales-targets.index') }}">
                                <span class="menu-icon menu-icon-sm"><i class="bi bi-bullseye text-warning"></i></span>
                                <span class="menu-title">{{ __('crm::sales_target.menu') }}</span>
                            </a>
                        </div>
                        @endcanTab
                        @canTab('crm.lead_tags')
                        <div class="menu-item">
                            <a class="menu-link {{ isset($active['crm_lead_tags']) ? 'active' : '' }}"
                               href="{{ route('admin.crm.lead-tags.index') }}">
                                <span class="menu-icon menu-icon-sm"><i class="bi bi-bookmark-star text-info"></i></span>
                                <span class="menu-title">{{ __('crm::lead_tag.menu') }}</span>
                            </a>
                        </div>
                        @endcanTab
                        @canTab('crm.custom_fields')
                        <div class="menu-item">
                            <a class="menu-link {{ isset($active['crm_custom_fields']) ? 'active' : '' }}"
                               href="{{ route('admin.crm.custom-fields.index') }}">
                                <span class="menu-icon menu-icon-sm"><i class="bi bi-ui-radios text-primary"></i></span>
                                <span class="menu-title">{{ __('crm::custom_field.menu') }}</span>
                            </a>
                        </div>
                        @endcanTab
                    </div>
                </div>
            @endif

            @if(auth()->user()?->canany(permission_keys(['settings.website', 'settings.system', 'settings.integrations', 'settings.seo'])))
                <div data-kt-menu-trigger="click"
                     class="menu-item menu-accordion {{ $websiteSettingsHere || isset($active['settings']) ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-gear text-secondary"></i></span>
                        <span class="menu-title">{{ __('Settings') }}</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion {{ $websiteSettingsHere ? 'show' : '' }}">
                        @canTab('settings.website')
                        <div class="menu-item">
                            <a class="menu-link {{ isset($active['websiteConfigurations']) ? 'active' : '' }}"
                               href="{{ route('admin.settings.index') }}">
                                <span class="menu-icon menu-icon-sm"><i class="bi bi-globe text-primary"></i></span>
                                <span class="menu-title">{{ __('Website Configurations') }}</span>
                            </a>
                        </div>
                        @endcanTab
                        @canTab('settings.system')
                        <div class="menu-item">
                            <a class="menu-link {{ isset($active['systemConfigurations']) ? 'active' : '' }}"
                               href="{{ route('admin.system-configurations.index') }}">
                                <span class="menu-icon menu-icon-sm"><i class="bi bi-cpu text-info"></i></span>
                                <span class="menu-title">{{ __('base::system.title') }}</span>
                            </a>
                        </div>
                        @endcanTab
                        @canTab('settings.integrations')
                        <div class="menu-item">
                            <a class="menu-link {{ isset($active['integrations']) ? 'active' : '' }}"
                               href="{{ route('admin.integrations.index') }}">
                                <span class="menu-icon menu-icon-sm"><i class="bi bi-link-45deg text-success"></i></span>
                                <span class="menu-title">{{ __('base::integrations.title') }}</span>
                            </a>
                        </div>
                        @endcanTab
                        @canTab('settings.seo')
                        <div class="menu-item">
                            <a class="menu-link {{ isset($active['seo']) ? 'active' : '' }}"
                               href="{{ route('admin.seo.index') }}">
                                <span class="menu-icon menu-icon-sm"><i class="bi bi-search text-warning"></i></span>
                                <span class="menu-title">{{ __('Seo Configurations') }}</span>
                            </a>
                        </div>
                        @endcanTab
                    </div>
                </div>
            @endif
            @canTab('settings.backups')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['backups']) ? 'active' : '' }}"
                       href="{{ route('admin.backups.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-database-down text-primary"></i></span>
                        <span class="menu-title">{{ __('base::backup.title') }}</span>
                    </a>
                </div>
            @endcanTab

            @canTab('support.visitors')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['visitors']) ? 'active' : '' }}"
                       href="{{ route('admin.visitors.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-eye text-info"></i></span>
                        <span class="menu-title">{{ __('Visitors') }}</span>
                    </a>
                </div>
            @endcanTab
            @canTab('support.subscribers')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['subscribers']) ? 'active' : '' }}"
                       href="{{ route('admin.subscribers.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-envelope-check text-success"></i></span>
                        <span class="menu-title">{{ __('Newsletter Subscribers') }}</span>
                    </a>
                </div>
            @endcanTab
            @canTab('support.search_keywords')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['search_keywords']) ? 'active' : '' }}"
                       href="{{ route('admin.search_keywords.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-search text-warning"></i></span>
                        <span class="menu-title">{{ __('Search Keywords') }}</span>
                    </a>
                </div>
            @endcanTab

            @canTab('system.logs')
                <div class="menu-item">
                    <a class="menu-link {{ isset($active['logs']) ? 'active' : '' }}"
                       href="{{ route('admin.logs.index') }}">
                        <span class="menu-icon menu-icon-sm"><i class="bi bi-bug text-danger"></i></span>
                        <span class="menu-title">{{ __('Logs & Bugs') }}</span>
                    </a>
                </div>
            @endcanTab
        </div>
    </div>
@endcanSection

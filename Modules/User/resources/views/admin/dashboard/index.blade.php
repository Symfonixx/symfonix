@section('title', __('Dashboard'))
@section('toolbar')
    <x-admin.breadcrumb :pageTitle="__('Dashboard')" :breadcrumbItems="[]"
                        :pageDescription="__('Welcome back! Here is an overview of your platform.')"/>
@endsection
<x-admin-layout>
    {{-- Welcome banner --}}
    <div class="admin-welcome-banner p-6 p-lg-8 mb-8">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-4 position-relative">
            <div class="d-flex align-items-center gap-4">
                <img src="{{ auth()->user()->avatar }}" alt="" class="welcome-avatar">
                <div>
                    <h2 class="text-white fw-bold fs-2 mb-1">
                        {{ __('Welcome back, :name!', ['name' => auth()->user()->name]) }}
                    </h2>
                    <p class="text-white opacity-75 mb-0 fs-6">
                        {{ now()->translatedFormat('l, F j, Y') }}
                    </p>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-light fw-semibold">
                    <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('View Website') }}
                </a>
                @can('CRM Management')
                    <a href="{{ route('admin.crm.dashboard') }}" class="btn btn-sm btn-light fw-semibold text-info">
                        <i class="bi bi-graph-up me-1"></i>{{ __('crm::dashboard.menu') }}
                    </a>
                    <a href="{{ route('admin.contact_forms.index') }}" class="btn btn-sm btn-light fw-semibold text-primary">
                        <i class="bi bi-envelope me-1"></i>{{ __('Messages') }}
                        @if(($stats['contacts'] ?? 0) > 0)
                            <span class="badge badge-circle badge-danger ms-1">{{ $stats['contacts'] }}</span>
                        @endif
                    </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Key metrics --}}
    <div class="row g-5 g-xl-8 mb-8">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card card card-body d-flex flex-row align-items-center gap-4 p-5">
                <div class="stat-icon bg-light-primary text-primary">
                    <i class="bi bi-eye"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stat-value text-gray-900">{{ number_format($visitorsStats['totalVisitorsCount']) }}</div>
                    <div class="stat-label">{{ __('Total Visitors') }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card card card-body d-flex flex-row align-items-center gap-4 p-5">
                <div class="stat-icon bg-light-success text-success">
                    <i class="bi bi-person-check"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stat-value text-gray-900">{{ number_format($visitorsStats['todayVisitorsCount']) }}</div>
                    <div class="stat-label">{{ __('Today Visitors') }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card card card-body d-flex flex-row align-items-center gap-4 p-5">
                <div class="stat-icon bg-light-warning text-warning">
                    <i class="bi bi-people"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stat-value text-gray-900">{{ number_format($stats['customers'] ?? 0) }}</div>
                    <div class="stat-label">{{ __('Customers') }}</div>
                    <a href="{{ route('admin.customers.index') }}" class="stat-link text-warning">{{ __('View all') }} →</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card card card-body d-flex flex-row align-items-center gap-4 p-5">
                <div class="stat-icon bg-light-danger text-danger">
                    <i class="bi bi-envelope"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stat-value text-gray-900">{{ number_format($stats['contacts'] ?? 0) }}</div>
                    <div class="stat-label">{{ __('Contacts') }}</div>
                    <a href="{{ route('admin.contact_forms.index') }}" class="stat-link text-danger">{{ __('View all') }} →</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 g-xl-8 mb-8">
        {{-- Top visited pages --}}
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold fs-4">{{ __('Top Visited Pages') }}</h3>
                    @can('Support Management')
                        <div class="card-toolbar">
                            <a href="{{ route('admin.visitors.index') }}" class="btn btn-sm btn-light-primary fw-semibold">
                                {{ __('View All') }}
                            </a>
                        </div>
                    @endcan
                </div>
                <div class="card-body pt-0">
                    @if($topVisitedPages->isEmpty())
                        <div class="table-empty-state py-8">
                            <div class="empty-icon"><i class="bi bi-bar-chart"></i></div>
                            <div class="text-muted">{{ __('No visitor data yet') }}</div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-row-dashed align-middle fs-6 gy-4 top-pages-table mb-0">
                                <thead>
                                <tr class="text-muted fw-bold fs-7 text-uppercase">
                                    <th>{{ __('Page') }}</th>
                                    <th class="text-end w-100px">{{ __('Visits') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($topVisitedPages as $topVisitedPage)
                                    <tr>
                                        <td>
                                            <a href="{{ $topVisitedPage->url }}" target="_blank" title="{{ $topVisitedPage->url }}">
                                                {{ $topVisitedPage->url }}
                                            </a>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge badge-light-primary visit-count">{{ number_format($topVisitedPage->total) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold fs-4">{{ __('Quick Actions') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        @can('CMS Management')
                            <div class="col-sm-6">
                                <a href="{{ route('admin.blogs.create') }}" class="quick-action-btn">
                                    <span class="qa-icon bg-light-primary text-primary"><i class="bi bi-pencil-square"></i></span>
                                    {{ __('New Blog Post') }}
                                </a>
                            </div>
                            <div class="col-sm-6">
                                <a href="{{ route('admin.pages.create') }}" class="quick-action-btn">
                                    <span class="qa-icon bg-light-info text-info"><i class="bi bi-file-earmark-plus"></i></span>
                                    {{ __('New Page') }}
                                </a>
                            </div>
                        @endcan
                        @can('CRM Management')
                            <div class="col-sm-6">
                                <a href="{{ route('admin.crm.dashboard') }}" class="quick-action-btn">
                                    <span class="qa-icon bg-light-info text-info"><i class="bi bi-graph-up-arrow"></i></span>
                                    {{ __('crm::dashboard.menu') }}
                                </a>
                            </div>
                            <div class="col-sm-6">
                                <a href="{{ route('admin.deals.create') }}" class="quick-action-btn">
                                    <span class="qa-icon bg-light-success text-success"><i class="bi bi-briefcase"></i></span>
                                    {{ __('crm::deal.actions.add') }}
                                </a>
                            </div>
                            <div class="col-sm-6">
                                <a href="{{ route('admin.companies.create') }}" class="quick-action-btn">
                                    <span class="qa-icon bg-light-warning text-warning"><i class="bi bi-building"></i></span>
                                    {{ __('crm::company.actions.add') }}
                                </a>
                            </div>
                        @endcan
                        @can('Hr Management')
                            <div class="col-sm-6">
                                <a href="{{ route('admin.employees.index') }}" class="quick-action-btn">
                                    <span class="qa-icon bg-light-danger text-danger"><i class="bi bi-person-plus"></i></span>
                                    {{ __('Manage Employees') }}
                                </a>
                            </div>
                        @endcan
                        @can('Settings Management')
                            <div class="col-sm-6">
                                <a href="{{ route('admin.settings.index') }}" class="quick-action-btn">
                                    <span class="qa-icon bg-light-dark text-dark"><i class="bi bi-gear"></i></span>
                                    {{ __('Settings') }}
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('CRM Management')
        @if(!empty($crmStats))
            <div class="row g-5 g-xl-8 mb-8">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title fw-bold fs-4">{{ __('crm::dashboard.title') }}</h3>
                            <div class="card-toolbar gap-2">
                                <a href="{{ route('admin.crm.sales-targets.index') }}" class="btn btn-sm btn-light fw-semibold">
                                    {{ __('crm::sales_target.menu') }}
                                </a>
                                <a href="{{ route('admin.crm.dashboard') }}" class="btn btn-sm btn-light-primary fw-semibold">
                                    {{ __('View All') }} →
                                </a>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row g-4">
                                <div class="col-sm-6 col-xl-3">
                                    <a href="{{ route('admin.companies.index') }}" class="d-block p-5 rounded bg-light-primary text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-primary">{{ number_format($crmStats['companies']) }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('crm::company.menu.companies') }}</div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="{{ route('admin.deals.index') }}" class="d-block p-5 rounded bg-light-success text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-success">{{ number_format($crmStats['open_deals']) }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('crm::deal.menu.deals') }}</div>
                                        <div class="text-muted fs-8 mt-1">{{ number_format($crmStats['deals']) }} {{ __('total') }}</div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="{{ route('admin.crm.dashboard') }}" class="d-block p-5 rounded bg-light-warning text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-warning">{{ number_format($crmStats['pipeline_value'], 0) }} {{ $crmStats['currency'] }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('crm::dashboard.metrics.pipeline_value') }}</div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="{{ route('admin.crm.dashboard') }}" class="d-block p-5 rounded bg-light-info text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-info">{{ number_format($crmStats['won_this_month']) }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('crm::dashboard.metrics.won_deals') }}</div>
                                        <div class="text-muted fs-8 mt-1">{{ __('This Month') }}</div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    {{-- Content & Users stats --}}
    <div class="row g-5 g-xl-8">
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold fs-4">{{ __('CMS Overview') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-4">
                        <div class="col-6">
                            <a href="{{ route('admin.pages.index') }}" class="d-block p-5 rounded bg-light-warning text-decoration-none h-100">
                                <div class="fs-2hx fw-bold text-warning">{{ $stats['pages'] ?? 0 }}</div>
                                <div class="fw-semibold text-gray-700">{{ __('Pages') }}</div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.blogs.index') }}" class="d-block p-5 rounded bg-light-primary text-decoration-none h-100">
                                <div class="fs-2hx fw-bold text-primary">{{ $stats['blogs'] ?? 0 }}</div>
                                <div class="fw-semibold text-gray-700">{{ __('Blogs') }}</div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.services.index') }}" class="d-block p-5 rounded bg-light-success text-decoration-none h-100">
                                <div class="fs-2hx fw-bold text-success">{{ $stats['services'] ?? 0 }}</div>
                                <div class="fw-semibold text-gray-700">{{ __('Services') }}</div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.leads.index') }}" class="d-block p-5 rounded bg-light-danger text-decoration-none h-100">
                                <div class="fs-2hx fw-bold text-danger">{{ $stats['leads'] ?? 0 }}</div>
                                <div class="fw-semibold text-gray-700">{{ __('Leads') }}</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold fs-4">{{ __('Users Overview') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-4">
                        <div class="col-6">
                            <a href="{{ route('admin.employees.index') }}" class="d-block p-5 rounded bg-light-warning text-decoration-none h-100">
                                <div class="fs-2hx fw-bold text-warning">{{ $stats['employees'] ?? 0 }}</div>
                                <div class="fw-semibold text-gray-700">{{ __('Employees') }}</div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.customers.index') }}" class="d-block p-5 rounded bg-light-primary text-decoration-none h-100">
                                <div class="fs-2hx fw-bold text-primary">{{ $stats['customers'] ?? 0 }}</div>
                                <div class="fw-semibold text-gray-700">{{ __('Customers') }}</div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.subscribers.index') }}" class="d-block p-5 rounded bg-light-danger text-decoration-none h-100">
                                <div class="fs-2hx fw-bold text-danger">{{ $stats['subscribers'] ?? 0 }}</div>
                                <div class="fw-semibold text-gray-700">{{ __('Newsletter Subscribers') }}</div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.contact_forms.index') }}" class="d-block p-5 rounded bg-light-success text-decoration-none h-100">
                                <div class="fs-2hx fw-bold text-success">{{ $stats['contacts'] ?? 0 }}</div>
                                <div class="fw-semibold text-gray-700">{{ __('Contacts') }}</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('App Monitoring')
        <div class="row g-5 g-xl-8 mt-2">
            <div class="col-sm-6 col-xl-3">
                <a href="/{{ config('telescope.path') }}" target="_blank"
                   class="quick-action-btn h-100">
                    <span class="qa-icon bg-light-info text-info"><i class="bi bi-bug"></i></span>
                    Telescope
                </a>
            </div>
            <div class="col-sm-6 col-xl-3">
                <a href="/{{ config('pulse.path') }}" target="_blank"
                   class="quick-action-btn h-100">
                    <span class="qa-icon bg-light-danger text-danger"><i class="bi bi-activity"></i></span>
                    Pulse
                </a>
            </div>
        </div>
    @endcan
</x-admin-layout>

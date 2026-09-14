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
                @canany(['crm.leads.view', 'crm.companies.view', 'sales.deals.view'])
                    <a href="{{ route('admin.crm.dashboard') }}" class="btn btn-sm btn-light fw-semibold text-info">
                        <i class="bi bi-graph-up me-1"></i>{{ __('crm::dashboard.menu') }}
                    </a>
                @endcanany
                @can('crm.inquiries.view')
                    <a href="{{ route('admin.contact_forms.index') }}" class="btn btn-sm btn-light fw-semibold text-primary">
                        <i class="bi bi-envelope me-1"></i>{{ __('Messages') }}
                        @if(($stats['contacts'] ?? 0) > 0)
                            <span class="badge badge-circle badge-danger ms-1">{{ $stats['contacts'] }}</span>
                        @endif
                    </a>
                @endcan
                @can('support.tickets.view')
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-light fw-semibold text-warning">
                        <i class="bi bi-ticket-detailed me-1"></i>{{ __('support::ticket.menu.tickets') }}
                        @if(($ticketStats['open'] ?? 0) > 0)
                            <span class="badge badge-circle badge-danger ms-1">{{ $ticketStats['open'] }}</span>
                        @endif
                    </a>
                @endcan
                @can('finance.dashboard.view')
                    <a href="{{ route('admin.finance.dashboard') }}" class="btn btn-sm btn-light fw-semibold text-success">
                        <i class="bi bi-currency-dollar me-1"></i>{{ __('finance::finance.menu.finance') }}
                    </a>
                @endcan
                @can('cms.testimonials.view')
                    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-sm btn-light fw-semibold text-primary">
                        <i class="bi bi-chat-quote me-1"></i>{{ __('Testimonials') }}
                        @if(($testimonialStats['pending_approval'] ?? 0) > 0)
                            <span class="badge badge-circle badge-danger ms-1">{{ $testimonialStats['pending_approval'] }}</span>
                        @endif
                    </a>
                @endcan
                @if(($notificationStats['unread'] ?? 0) > 0)
                    <span class="btn btn-sm btn-light fw-semibold text-danger disabled">
                        <i class="bi bi-bell me-1"></i>{{ __('user::dashboard.unread_notifications') }}
                        <span class="badge badge-circle badge-danger ms-1">{{ $notificationStats['unread'] }}</span>
                    </span>
                @endif
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
                <div class="stat-icon bg-light-info text-info">
                    <i class="bi bi-calendar-week"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stat-value text-gray-900">{{ number_format($visitorsStats['weekVisitorsCount'] ?? 0) }}</div>
                    <div class="stat-label">{{ __('user::dashboard.week_visitors') }}</div>
                </div>
            </div>
        </div>
        @can('sales.customers.view')
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
        @endcan
        @can('support.tickets.view')
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card card card-body d-flex flex-row align-items-center gap-4 p-5">
                <div class="stat-icon bg-light-warning text-warning">
                    <i class="bi bi-ticket-detailed"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stat-value text-gray-900">{{ number_format($ticketStats['open'] ?? 0) }}</div>
                    <div class="stat-label">{{ __('support::ticket.dashboard.open_tickets') }}</div>
                    <a href="{{ route('admin.tickets.index') }}" class="stat-link text-warning">{{ __('View all') }} →</a>
                </div>
            </div>
        </div>
        @endcan
        @can('cms.testimonials.view')
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card card card-body d-flex flex-row align-items-center gap-4 p-5">
                <div class="stat-icon bg-light-warning text-warning">
                    <i class="bi bi-chat-quote"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stat-value text-gray-900">{{ number_format($testimonialStats['pending_approval'] ?? 0) }}</div>
                    <div class="stat-label">{{ __('testimonial::notifications.dashboard.pending_approval') }}</div>
                    <a href="{{ route('admin.testimonials.index') }}" class="stat-link text-warning">{{ __('View all') }} →</a>
                </div>
            </div>
        </div>
        @endcan
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
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card card card-body d-flex flex-row align-items-center gap-4 p-5">
                <div class="stat-icon bg-light-primary text-primary">
                    <i class="bi bi-envelope-plus"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stat-value text-gray-900">{{ number_format($stats['contacts_this_month'] ?? 0) }}</div>
                    <div class="stat-label">{{ __('user::dashboard.contacts_this_month') }}</div>
                </div>
            </div>
        </div>
        @if(($notificationStats['unread'] ?? 0) > 0)
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card card card-body d-flex flex-row align-items-center gap-4 p-5">
                <div class="stat-icon bg-light-danger text-danger">
                    <i class="bi bi-bell"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stat-value text-gray-900">{{ number_format($notificationStats['unread']) }}</div>
                    <div class="stat-label">{{ __('user::dashboard.unread_notifications') }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Visits by month --}}
    <div class="row g-5 g-xl-8 mb-8">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold fs-4">{{ __('user::dashboard.visits_by_month') }}</h3>
                    @can('support.visitors.view')
                        <div class="card-toolbar">
                            <a href="{{ route('admin.visitors.index') }}" class="btn btn-sm btn-light-primary fw-semibold">
                                {{ __('View All') }}
                            </a>
                        </div>
                    @endcan
                </div>
                <div class="card-body pt-0">
                    <div class="visits-month-chart-wrap">
                        <canvas id="visitsByMonthChart" height="280"></canvas>
                    </div>
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
                    @can('support.visitors.view')
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

        {{-- Top referrer links --}}
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold fs-4">{{ __('user::dashboard.top_referrers') }}</h3>
                    @can('support.visitors.view')
                        <div class="card-toolbar">
                            <a href="{{ route('admin.visitors.index') }}" class="btn btn-sm btn-light-primary fw-semibold">
                                {{ __('View All') }}
                            </a>
                        </div>
                    @endcan
                </div>
                <div class="card-body pt-0">
                    @if($topReferrers->isEmpty())
                        <div class="table-empty-state py-8">
                            <div class="empty-icon"><i class="bi bi-link-45deg"></i></div>
                            <div class="text-muted">{{ __('user::dashboard.no_referrers') }}</div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-row-dashed align-middle fs-6 gy-4 top-pages-table mb-0">
                                <thead>
                                <tr class="text-muted fw-bold fs-7 text-uppercase">
                                    <th>{{ __('user::dashboard.referrer') }}</th>
                                    <th class="text-end w-100px">{{ __('Visits') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($topReferrers as $referrer)
                                    <tr>
                                        <td>
                                            <a href="{{ $referrer->referrer }}" target="_blank" rel="noopener noreferrer" title="{{ $referrer->referrer }}">
                                                {{ $referrer->referrer }}
                                            </a>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge badge-light-info visit-count">{{ number_format($referrer->total) }}</span>
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
    </div>

    {{-- Recent activity --}}
    <div class="row g-5 g-xl-8 mb-8">
        @can('crm.inquiries.view')
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold fs-5">{{ __('user::dashboard.recent_contacts') }}</h3>
                    <div class="card-toolbar">
                        <a href="{{ route('admin.contact_forms.index') }}" class="btn btn-sm btn-light-primary fw-semibold">
                            {{ __('View All') }}
                        </a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    @if($recentContacts->isEmpty())
                        <div class="table-empty-state py-8">
                            <div class="empty-icon"><i class="bi bi-envelope"></i></div>
                            <div class="text-muted">{{ __('user::dashboard.no_contacts') }}</div>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-4">
                            @foreach($recentContacts as $contact)
                                <a href="{{ route('admin.contact_forms.edit', $contact) }}" class="d-flex flex-column text-decoration-none">
                                    <span class="text-gray-800 fw-semibold">{{ $contact->name }}</span>
                                    <span class="text-muted fs-7">{{ \Illuminate\Support\Str::limit($contact->subject ?: $contact->message, 50) }}</span>
                                    <span class="text-muted fs-8 mt-1">{{ $contact->created_at->diffForHumans() }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endcan

        @can('crm.leads.view')
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold fs-5">{{ __('user::dashboard.recent_leads') }}</h3>
                    <div class="card-toolbar gap-2">
                        @if(!empty($leadStats))
                            <span class="badge badge-light-primary">{{ number_format($leadStats['new']) }} {{ __('user::dashboard.leads.new') }}</span>
                        @endif
                        <a href="{{ route('admin.leads.index') }}" class="btn btn-sm btn-light-primary fw-semibold">
                            {{ __('View All') }}
                        </a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    @if($recentLeads->isEmpty())
                        <div class="table-empty-state py-8">
                            <div class="empty-icon"><i class="bi bi-person-lines-fill"></i></div>
                            <div class="text-muted">{{ __('user::dashboard.no_leads') }}</div>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-4">
                            @foreach($recentLeads as $lead)
                                <a href="{{ route('admin.leads.show', $lead) }}" class="d-flex align-items-start justify-content-between gap-3 text-decoration-none">
                                    <div>
                                        <span class="text-gray-800 fw-semibold d-block">{{ $lead->name }}</span>
                                        <span class="text-muted fs-7">{{ $lead->email }}</span>
                                    </div>
                                    <span class="badge badge-light-{{ \Modules\CRM\Models\Lead::statusBadgeColor($lead->status) }}">
                                        {{ __('crm::lead.status.'.$lead->status) }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endcan

        <div class="col-xl-{{ auth()->user()?->canAny(['crm.inquiries.view', 'crm.leads.view']) ? '4' : '12' }}">
            <div class="card h-100">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold fs-5">{{ __('user::dashboard.recent_notifications') }}</h3>
                    @if(($notificationStats['unread'] ?? 0) > 0)
                        <div class="card-toolbar">
                            <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light fw-semibold">
                                    {{ __('user::portal.notifications.mark_all_read') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
                <div class="card-body pt-0">
                    @if($recentNotifications->isEmpty())
                        <div class="table-empty-state py-8">
                            <div class="empty-icon"><i class="bi bi-bell"></i></div>
                            <div class="text-muted">{{ __('user::dashboard.no_notifications') }}</div>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-4">
                            @foreach($recentNotifications as $notification)
                                @php $data = $notification->data; @endphp
                                <div class="d-flex align-items-start gap-3 {{ $notification->read_at ? '' : 'fw-semibold' }}">
                                    <span class="bullet bullet-dot mt-2 {{ $notification->read_at ? 'bg-secondary' : 'bg-primary' }}"></span>
                                    <div class="flex-grow-1">
                                        @if(!empty($data['url']))
                                            <a href="{{ $data['url'] }}" class="text-gray-800 text-hover-primary text-decoration-none">
                                                {{ $data['message'] ?? __('Notification') }}
                                            </a>
                                        @else
                                            <span class="text-gray-800">{{ $data['message'] ?? __('Notification') }}</span>
                                        @endif
                                        <div class="text-muted fs-8 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @canany(['crm.leads.view', 'crm.companies.view', 'sales.deals.view'])
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
    @endcanany

    @can('finance.dashboard.view')
        @if(!empty($financeStats))
            <div class="row g-5 g-xl-8 mb-8">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title fw-bold fs-4">{{ __('user::dashboard.finance.title') }}</h3>
                            <div class="card-toolbar gap-2">
                                <a href="{{ route('admin.finance.invoices.index') }}" class="btn btn-sm btn-light fw-semibold">
                                    {{ __('finance::invoice.menu') }}
                                </a>
                                <a href="{{ route('admin.finance.accounts-receivable') }}" class="btn btn-sm btn-light fw-semibold">
                                    {{ __('finance::accounts_receivable.menu') }}
                                </a>
                                <a href="{{ route('admin.finance.dashboard') }}" class="btn btn-sm btn-light-primary fw-semibold">
                                    {{ __('View All') }} →
                                </a>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row g-4 mb-6">
                                <div class="col-sm-6 col-xl-3">
                                    <a href="{{ route('admin.finance.dashboard') }}" class="d-block p-5 rounded bg-light-success text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-success">{{ number_format($financeStats['revenue_this_month'], 0) }} {{ $financeStats['currency'] }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('user::dashboard.finance.revenue_this_month') }}</div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="{{ route('admin.finance.accounts-receivable') }}" class="d-block p-5 rounded bg-light-warning text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-warning">{{ number_format($financeStats['outstanding'], 0) }} {{ $financeStats['currency'] }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('user::dashboard.finance.outstanding') }}</div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="{{ route('admin.finance.invoices.index') }}" class="d-block p-5 rounded bg-light-danger text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-danger">{{ number_format($financeStats['open_invoices']) }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('user::dashboard.finance.open_invoices') }}</div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="{{ route('admin.finance.invoices.index') }}" class="d-block p-5 rounded bg-light-primary text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-primary">{{ number_format($financeStats['paid_this_month'], 0) }} {{ $financeStats['currency'] }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('user::dashboard.finance.paid_this_month') }}</div>
                                    </a>
                                </div>
                            </div>

                            <h4 class="fw-bold fs-5 mb-4">{{ __('user::dashboard.finance.recent_invoices') }}</h4>
                            @if($recentInvoices->isEmpty())
                                <div class="table-empty-state py-8">
                                    <div class="empty-icon"><i class="bi bi-receipt"></i></div>
                                    <div class="text-muted">{{ __('user::dashboard.finance.no_invoices') }}</div>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-row-dashed align-middle fs-6 gy-4 mb-0">
                                        <thead>
                                        <tr class="text-muted fw-bold fs-7 text-uppercase">
                                            <th>{{ __('finance::invoice.fields.invoice_number') }}</th>
                                            <th>{{ __('finance::invoice.fields.company') }}</th>
                                            <th>{{ __('finance::invoice.fields.total') }}</th>
                                            <th>{{ __('finance::invoice.fields.status') }}</th>
                                            <th>{{ __('Created At') }}</th>
                                            <th class="text-end"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($recentInvoices as $invoice)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.finance.invoices.show', $invoice) }}" class="text-gray-800 text-hover-primary fw-bold">
                                                        {{ $invoice->invoice_number }}
                                                    </a>
                                                </td>
                                                <td>{{ $invoice->company?->name ?? __('N/A') }}</td>
                                                <td>{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td>
                                                <td>
                                                    <span class="badge badge-light-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : 'primary') }}">
                                                        {{ __('finance::invoice.status.'.$invoice->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $invoice->created_at->diffForHumans() }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.finance.invoices.pdf', $invoice) }}"
                                                       class="btn btn-sm btn-light-primary me-1"
                                                       title="{{ __('finance::invoice.actions.download_pdf') }}">
                                                        <i class="bi bi-file-pdf"></i>
                                                    </a>
                                                    <a href="{{ route('admin.finance.invoices.show', $invoice) }}" class="btn btn-sm btn-light-primary">
                                                        {{ __('View') }}
                                                    </a>
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
            </div>
        @endif
    @endcan

    @can('project.projects.view')
        @if(!empty($projectStats))
            <div class="row g-5 g-xl-8 mb-8">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title fw-bold fs-4">{{ __('user::dashboard.projects.title') }}</h3>
                            <div class="card-toolbar gap-2">
                                <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-light-primary fw-semibold">
                                    {{ __('View All') }} →
                                </a>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row g-4 mb-6">
                                <div class="col-sm-6 col-xl-4">
                                    <a href="{{ route('admin.projects.index') }}" class="d-block p-5 rounded bg-light-primary text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-primary">{{ number_format($projectStats['total']) }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('project::project.menu.projects') }}</div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-xl-4">
                                    <a href="{{ route('admin.projects.index') }}" class="d-block p-5 rounded bg-light-success text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-success">{{ number_format($projectStats['active']) }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('user::dashboard.projects.active') }}</div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-xl-4">
                                    <a href="{{ route('admin.projects.index') }}" class="d-block p-5 rounded bg-light-warning text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-warning">{{ number_format($projectStats['unpaid']) }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('user::dashboard.projects.unpaid') }}</div>
                                    </a>
                                </div>
                            </div>

                            <h4 class="fw-bold fs-5 mb-4">{{ __('user::dashboard.projects.recent') }}</h4>
                            @if($recentProjects->isEmpty())
                                <div class="table-empty-state py-8">
                                    <div class="empty-icon"><i class="bi bi-briefcase"></i></div>
                                    <div class="text-muted">{{ __('user::dashboard.projects.no_projects') }}</div>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-row-dashed align-middle fs-6 gy-4 mb-0">
                                        <thead>
                                        <tr class="text-muted fw-bold fs-7 text-uppercase">
                                            <th>{{ __('project::project.fields.title') }}</th>
                                            <th>{{ __('project::project.fields.company') }}</th>
                                            <th>{{ __('project::project.fields.status') }}</th>
                                            <th>{{ __('project::project.fields.payment_status') }}</th>
                                            <th>{{ __('Created At') }}</th>
                                            <th class="text-end"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($recentProjects as $project)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.projects.show', $project) }}" class="text-gray-800 text-hover-primary fw-bold">
                                                        {{ \Illuminate\Support\Str::limit($project->title, 40) }}
                                                    </a>
                                                </td>
                                                <td>{{ $project->company?->name ?? __('N/A') }}</td>
                                                <td>
                                                    @if($project->status)
                                                        <span class="badge" style="background-color: {{ $project->status->color_code }}20; color: {{ $project->status->color_code }}">
                                                            {{ $project->status->name }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-light-{{ $project->payment_status === 'fully_paid' ? 'success' : ($project->payment_status === 'partially_paid' ? 'warning' : 'danger') }}">
                                                        {{ __('project::project.payment_status.'.$project->payment_status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $project->created_at->diffForHumans() }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-sm btn-light-primary">
                                                        {{ __('View') }}
                                                    </a>
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
            </div>
        @endif
    @endcan

    @can('product.catalog.view')
        @if(!empty($productStats))
            <div class="row g-5 g-xl-8 mb-8">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title fw-bold fs-4">{{ __('user::dashboard.products.title') }}</h3>
                            <div class="card-toolbar gap-2">
                                <a href="{{ route('admin.finance.product-sales.index') }}" class="btn btn-sm btn-light fw-semibold">
                                    {{ __('finance::product_sale.menu') }}
                                </a>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-light-primary fw-semibold">
                                    {{ __('View All') }} →
                                </a>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row g-4 mb-6">
                                <div class="col-sm-6 col-xl-3">
                                    <a href="{{ route('admin.products.index') }}" class="d-block p-5 rounded bg-light-primary text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-primary">{{ number_format($productStats['total']) }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('product::product.menu.products') }}</div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="{{ route('admin.products.index') }}" class="d-block p-5 rounded bg-light-success text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-success">{{ number_format($productStats['published']) }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('user::dashboard.products.published') }}</div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="{{ route('admin.finance.product-sales.index') }}" class="d-block p-5 rounded bg-light-info text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-info">{{ number_format($productStats['sales_this_month']) }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('user::dashboard.products.sales_this_month') }}</div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="{{ route('admin.finance.product-sales.index') }}" class="d-block p-5 rounded bg-light-warning text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-warning">{{ number_format($productStats['revenue_this_month'], 0) }} {{ $productStats['currency'] }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('user::dashboard.products.revenue_this_month') }}</div>
                                    </a>
                                </div>
                            </div>

                            <h4 class="fw-bold fs-5 mb-4">{{ __('user::dashboard.products.recent_sales') }}</h4>
                            @if($recentProductSales->isEmpty())
                                <div class="table-empty-state py-8">
                                    <div class="empty-icon"><i class="bi bi-box-seam"></i></div>
                                    <div class="text-muted">{{ __('user::dashboard.products.no_sales') }}</div>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-row-dashed align-middle fs-6 gy-4 mb-0">
                                        <thead>
                                        <tr class="text-muted fw-bold fs-7 text-uppercase">
                                            <th>{{ __('product::product.fields.name') }}</th>
                                            <th>{{ __('finance::invoice.fields.company') }}</th>
                                            <th>{{ __('finance::invoice.fields.total') }}</th>
                                            <th>{{ __('finance::product_sale.fields.sold_at') }}</th>
                                            <th class="text-end"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($recentProductSales as $sale)
                                            <tr>
                                                <td class="fw-semibold text-gray-800">{{ $sale->product?->name ?? __('N/A') }}</td>
                                                <td>{{ $sale->company?->name ?? __('N/A') }}</td>
                                                <td>{{ number_format($sale->total_amount, 2) }} {{ $sale->currency }}</td>
                                                <td>{{ $sale->sold_at?->diffForHumans() ?? $sale->created_at->diffForHumans() }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.finance.product-sales.index') }}" class="btn btn-sm btn-light-primary">
                                                        {{ __('View') }}
                                                    </a>
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
            </div>
        @endif
    @endcan

    @can('support.tickets.view')
        @if(!empty($ticketStats))
            <div class="row g-5 g-xl-8 mb-8">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title fw-bold fs-4">{{ __('support::ticket.dashboard.title') }}</h3>
                            <div class="card-toolbar gap-2">
                                <a href="{{ route('admin.ticket_categories.index') }}" class="btn btn-sm btn-light fw-semibold">
                                    {{ __('support::ticket.menu.categories') }}
                                </a>
                                <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-light-primary fw-semibold">
                                    {{ __('View All') }} →
                                </a>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row g-4 mb-6">
                                <div class="col-sm-6 col-xl-3">
                                    <a href="{{ route('admin.tickets.index', ['status' => 'open']) }}" class="d-block p-5 rounded bg-light-warning text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-warning">{{ number_format($ticketStats['open']) }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('support::ticket.dashboard.open_tickets') }}</div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="{{ route('admin.tickets.index', ['status' => 'resolved']) }}" class="d-block p-5 rounded bg-light-success text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-success">{{ number_format($ticketStats['resolved']) }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('support::ticket.status.resolved') }}</div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="{{ route('admin.tickets.index', ['status' => 'closed']) }}" class="d-block p-5 rounded bg-light-secondary text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-gray-700">{{ number_format($ticketStats['closed']) }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('support::ticket.status.closed') }}</div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="{{ route('admin.tickets.index') }}" class="d-block p-5 rounded bg-light-primary text-decoration-none h-100">
                                        <div class="fs-2hx fw-bold text-primary">{{ number_format($ticketStats['total']) }}</div>
                                        <div class="fw-semibold text-gray-700">{{ __('support::ticket.dashboard.total_tickets') }}</div>
                                    </a>
                                </div>
                            </div>

                            <h4 class="fw-bold fs-5 mb-4">{{ __('support::ticket.dashboard.recent_tickets') }}</h4>
                            @if($recentTickets->isEmpty())
                                <div class="table-empty-state py-8">
                                    <div class="empty-icon"><i class="bi bi-ticket-detailed"></i></div>
                                    <div class="text-muted">{{ __('support::ticket.dashboard.no_tickets') }}</div>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-row-dashed align-middle fs-6 gy-4 mb-0">
                                        <thead>
                                        <tr class="text-muted fw-bold fs-7 text-uppercase">
                                            <th>{{ __('support::ticket.fields.ticket_number') }}</th>
                                            <th>{{ __('support::ticket.fields.subject') }}</th>
                                            <th>{{ __('support::ticket.fields.customer') }}</th>
                                            <th>{{ __('support::ticket.fields.priority') }}</th>
                                            <th>{{ __('support::ticket.fields.status') }}</th>
                                            <th>{{ __('Created At') }}</th>
                                            <th class="text-end"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($recentTickets as $ticket)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.tickets.show', $ticket) }}" class="text-gray-800 text-hover-primary fw-bold">
                                                        {{ $ticket->ticket_number }}
                                                    </a>
                                                </td>
                                                <td>{{ \Illuminate\Support\Str::limit($ticket->subject, 40) }}</td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-gray-800 fw-semibold">{{ $ticket->user?->name }}</span>
                                                        <span class="text-muted fs-7">{{ $ticket->user?->email }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light-{{ $ticket->priority === 'urgent' ? 'danger' : ($ticket->priority === 'high' ? 'warning' : 'primary') }}">
                                                        {{ __('support::ticket.priority.'.$ticket->priority) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light-{{ $ticket->status === 'closed' || $ticket->status === 'resolved' ? 'success' : ($ticket->status === 'in_progress' ? 'info' : 'secondary') }}">
                                                        {{ __('support::ticket.status.'.$ticket->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $ticket->created_at->diffForHumans() }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-light-primary">
                                                        {{ __('View') }}
                                                    </a>
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
            </div>
        @endif
    @endcan

    {{-- Content & Users stats --}}
    <div class="row g-5 g-xl-8">
        @canany(['cms.pages.view', 'cms.blogs.view', 'services.catalog.view', 'crm.leads.view'])
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold fs-4">{{ __('CMS Overview') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-4">
                        @can('cms.pages.view')
                        <div class="col-6">
                            <a href="{{ route('admin.pages.index') }}" class="d-block p-5 rounded bg-light-warning text-decoration-none h-100">
                                <div class="fs-2hx fw-bold text-warning">{{ $stats['pages'] ?? 0 }}</div>
                                <div class="fw-semibold text-gray-700">{{ __('Pages') }}</div>
                            </a>
                        </div>
                        @endcan
                        @can('cms.blogs.view')
                        <div class="col-6">
                            <a href="{{ route('admin.blogs.index') }}" class="d-block p-5 rounded bg-light-primary text-decoration-none h-100">
                                <div class="fs-2hx fw-bold text-primary">{{ $stats['blogs'] ?? 0 }}</div>
                                <div class="fw-semibold text-gray-700">{{ __('Blogs') }}</div>
                            </a>
                        </div>
                        @endcan
                        @can('services.catalog.view')
                        <div class="col-6">
                            <a href="{{ route('admin.services.index') }}" class="d-block p-5 rounded bg-light-success text-decoration-none h-100">
                                <div class="fs-2hx fw-bold text-success">{{ $stats['services'] ?? 0 }}</div>
                                <div class="fw-semibold text-gray-700">{{ __('Services') }}</div>
                            </a>
                        </div>
                        @endcan
                        @can('crm.leads.view')
                        <div class="col-6">
                            <a href="{{ route('admin.leads.index') }}" class="d-block p-5 rounded bg-light-danger text-decoration-none h-100">
                                <div class="fs-2hx fw-bold text-danger">{{ $stats['leads'] ?? 0 }}</div>
                                <div class="fw-semibold text-gray-700">{{ __('Leads') }}</div>
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @endcanany

        @canany(['hr.employees.view', 'sales.customers.view', 'support.subscribers.view', 'crm.inquiries.view', 'support.tickets.view'])
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold fs-4">{{ __('Users Overview') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-4">
                        @can('hr.employees.view')
                        <div class="col-6">
                            <a href="{{ route('admin.employees.index') }}" class="d-block p-5 rounded bg-light-warning text-decoration-none h-100">
                                <div class="fs-2hx fw-bold text-warning">{{ $stats['employees'] ?? 0 }}</div>
                                <div class="fw-semibold text-gray-700">{{ __('Employees') }}</div>
                            </a>
                        </div>
                        @endcan
                        @can('sales.customers.view')
                        <div class="col-6">
                            <a href="{{ route('admin.customers.index') }}" class="d-block p-5 rounded bg-light-primary text-decoration-none h-100">
                                <div class="fs-2hx fw-bold text-primary">{{ $stats['customers'] ?? 0 }}</div>
                                <div class="fw-semibold text-gray-700">{{ __('Customers') }}</div>
                            </a>
                        </div>
                        @endcan
                        @can('support.subscribers.view')
                        <div class="col-6">
                            <a href="{{ route('admin.subscribers.index') }}" class="d-block p-5 rounded bg-light-danger text-decoration-none h-100">
                                <div class="fs-2hx fw-bold text-danger">{{ $stats['subscribers'] ?? 0 }}</div>
                                <div class="fw-semibold text-gray-700">{{ __('Newsletter Subscribers') }}</div>
                            </a>
                        </div>
                        @endcan
                        @can('crm.inquiries.view')
                        <div class="col-6">
                            <a href="{{ route('admin.contact_forms.index') }}" class="d-block p-5 rounded bg-light-success text-decoration-none h-100">
                                <div class="fs-2hx fw-bold text-success">{{ $stats['contacts'] ?? 0 }}</div>
                                <div class="fw-semibold text-gray-700">{{ __('Contacts') }}</div>
                            </a>
                        </div>
                        @endcan
                        @can('support.tickets.view')
                        <div class="col-6">
                            <a href="{{ route('admin.tickets.index') }}" class="d-block p-5 rounded bg-light-warning text-decoration-none h-100">
                                <div class="fs-2hx fw-bold text-warning">{{ $ticketStats['open'] ?? 0 }}</div>
                                <div class="fw-semibold text-gray-700">{{ __('support::ticket.dashboard.open_tickets') }}</div>
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @endcanany
    </div>

    @can('system.monitoring.view')
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

    {{-- Fixed quick actions FAB --}}
    <div class="quick-actions-fab" tabindex="0">
        <button type="button" class="quick-actions-fab__trigger" aria-label="{{ __('Quick Actions') }}">
            <i class="bi bi-lightning-charge-fill"></i>
        </button>
        <div class="quick-actions-fab__panel">
            <div class="quick-actions-fab__title">{{ __('Quick Actions') }}</div>
            <div class="quick-actions-fab__list">
                @can('cms.blogs.create')
                    <a href="{{ route('admin.blogs.create') }}" class="quick-action-btn">
                        <span class="qa-icon bg-light-primary text-primary"><i class="bi bi-pencil-square"></i></span>
                        {{ __('New Blog Post') }}
                    </a>
                @endcan
                @can('cms.pages.create')
                    <a href="{{ route('admin.pages.create') }}" class="quick-action-btn">
                        <span class="qa-icon bg-light-info text-info"><i class="bi bi-file-earmark-plus"></i></span>
                        {{ __('New Page') }}
                    </a>
                @endcan
                @can('overview.crm_analytics.view')
                    <a href="{{ route('admin.crm.dashboard') }}" class="quick-action-btn">
                        <span class="qa-icon bg-light-info text-info"><i class="bi bi-graph-up-arrow"></i></span>
                        {{ __('crm::dashboard.menu') }}
                    </a>
                @endcan
                @can('sales.deals.create')
                    <a href="{{ route('admin.deals.create') }}" class="quick-action-btn">
                        <span class="qa-icon bg-light-success text-success"><i class="bi bi-briefcase"></i></span>
                        {{ __('crm::deal.actions.add') }}
                    </a>
                @endcan
                @can('crm.companies.create')
                    <a href="{{ route('admin.companies.create') }}" class="quick-action-btn">
                        <span class="qa-icon bg-light-warning text-warning"><i class="bi bi-building"></i></span>
                        {{ __('crm::company.actions.add') }}
                    </a>
                @endcan
                @can('crm.leads.create')
                    <a href="{{ route('admin.leads.create') }}" class="quick-action-btn">
                        <span class="qa-icon bg-light-danger text-danger"><i class="bi bi-person-plus"></i></span>
                        {{ __('crm::lead.actions.add') }}
                    </a>
                @endcan
                @can('finance.invoices.create')
                    <a href="{{ route('admin.finance.invoices.create') }}" class="quick-action-btn">
                        <span class="qa-icon bg-light-success text-success"><i class="bi bi-receipt"></i></span>
                        {{ __('finance::invoice.actions.create') }}
                    </a>
                @endcan
                @can('project.projects.create')
                    <a href="{{ route('admin.projects.create') }}" class="quick-action-btn">
                        <span class="qa-icon bg-light-primary text-primary"><i class="bi bi-briefcase"></i></span>
                        {{ __('project::project.actions.add') }}
                    </a>
                @endcan
                @can('product.catalog.create')
                    <a href="{{ route('admin.products.create') }}" class="quick-action-btn">
                        <span class="qa-icon bg-light-warning text-warning"><i class="bi bi-box-seam"></i></span>
                        {{ __('product::product.actions.add') }}
                    </a>
                @endcan
                @can('support.tickets.view')
                    <a href="{{ route('admin.tickets.index') }}" class="quick-action-btn">
                        <span class="qa-icon bg-light-warning text-warning"><i class="bi bi-ticket-detailed"></i></span>
                        {{ __('support::ticket.menu.tickets') }}
                    </a>
                @endcan
                @can('services.catalog.create')
                    <a href="{{ route('admin.services.create') }}" class="quick-action-btn">
                        <span class="qa-icon bg-light-success text-success"><i class="bi bi-grid"></i></span>
                        {{ __('New Service') }}
                    </a>
                @endcan
                @can('hr.employees.view')
                    <a href="{{ route('admin.employees.index') }}" class="quick-action-btn">
                        <span class="qa-icon bg-light-danger text-danger"><i class="bi bi-person-plus"></i></span>
                        {{ __('Manage Employees') }}
                    </a>
                @endcan
                @can('settings.website.view')
                    <a href="{{ route('admin.settings.index') }}" class="quick-action-btn">
                        <span class="qa-icon bg-light-dark text-dark"><i class="bi bi-gear"></i></span>
                        {{ __('Settings') }}
                    </a>
                @endcan
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            (function () {
                const visitsCtx = document.getElementById('visitsByMonthChart');
                if (!visitsCtx) return;

                const visitsData = @json($visitsByMonth);
                const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
                const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
                const tickColor = isDark ? '#A1A5B7' : '#7E8299';

                new Chart(visitsCtx, {
                    type: 'line',
                    data: {
                        labels: visitsData.map(item => item.label),
                        datasets: [{
                            label: @json(__('Visits')),
                            data: visitsData.map(item => item.total),
                            borderColor: 'rgb(54, 153, 255)',
                            backgroundColor: 'rgba(54, 153, 255, 0.12)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            pointBackgroundColor: 'rgb(54, 153, 255)',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => `${ctx.parsed.y.toLocaleString()} {{ __('Visits') }}`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0, color: tickColor },
                                grid: { color: gridColor }
                            },
                            x: {
                                ticks: { color: tickColor, maxRotation: 0 },
                                grid: { display: false }
                            }
                        }
                    }
                });
            })();
        </script>
    @endpush
</x-admin-layout>

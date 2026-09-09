@section('title', __('crm::subscription.pages.show_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::subscription.menu.subscriptions'), 'url' => route('admin.subscriptions.index')],
            ['label' => __('crm::subscription.pages.show_title')],
        ];
        $statusColor = match($subscription->status) {
            'active' => 'success',
            'trial' => 'info',
            'paused' => 'warning',
            'cancelled', 'expired' => 'danger',
            default => 'secondary',
        };
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::subscription.pages.show_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.subscriptions.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::subscription.actions.back_to_list') }}
        </a>
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.companies.show', $subscription->company_id) }}">
            <i class="bi bi-building me-1"></i>{{ $subscription->company?->name }}
        </a>
        <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.subscriptions.edit', $subscription->id) }}">
            <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <div class="card sx-show-hero mb-8">
        <div class="card-body p-6 p-lg-8">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-5">
                <div class="d-flex align-items-center gap-4">
                    <span class="sx-avatar"><i class="bi bi-arrow-repeat"></i></span>
                    <div>
                        <h2 class="text-white fw-bold mb-2">{{ $subscription->name }}</h2>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="badge badge-light-{{ $statusColor }}">
                                {{ __('crm::subscription.status.'.$subscription->status) }}
                            </span>
                            @if($subscription->company)
                                <span class="text-white opacity-75 fs-7">
                                    <i class="bi bi-building me-1"></i>{{ $subscription->company->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <div class="text-white opacity-75 fs-7 mb-1">{{ __('crm::subscription.fields.amount') }}</div>
                    <div class="text-white fw-bold fs-2x">
                        {{ number_format($subscription->amount, 2) }} {{ $subscription->currency }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('crm::subscription.pages.show_title') }}</h3>
        </div>
        <div class="card-body">
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::subscription.fields.company') }}</div>
                <div class="col-md-9">
                    <a href="{{ route('admin.companies.show', $subscription->company_id) }}" class="text-hover-primary">
                        {{ $subscription->company?->name ?: __('N/A') }}
                    </a>
                </div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::subscription.fields.services') }}</div>
                <div class="col-md-9">
                    @if($subscription->services->isNotEmpty())
                        @foreach($subscription->services as $service)
                            <a href="{{ route('admin.services.edit', $service->id) }}" class="badge badge-light-primary me-1 mb-1 text-hover-primary">
                                {{ $service->getTranslation('title', app()->getLocale()) }}
                            </a>
                        @endforeach
                    @elseif($subscription->service)
                        <a href="{{ route('admin.services.edit', $subscription->service_id) }}" class="text-hover-primary">
                            {{ $subscription->service->getTranslation('title', app()->getLocale()) }}
                        </a>
                    @else
                        {{ __('N/A') }}
                    @endif
                </div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::subscription.fields.amount') }}</div>
                <div class="col-md-9">{{ number_format($subscription->amount, 2) }} {{ $subscription->currency }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::subscription.fields.billing_cycle') }}</div>
                <div class="col-md-9">{{ __('crm::subscription.billing_cycle.'.$subscription->billing_cycle) }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::subscription.fields.starts_at') }}</div>
                <div class="col-md-9">{{ $subscription->starts_at?->format('Y-m-d') ?: __('N/A') }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::subscription.fields.ends_at') }}</div>
                <div class="col-md-9">{{ $subscription->ends_at?->format('Y-m-d') ?: __('N/A') }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::subscription.fields.renewal_at') }}</div>
                <div class="col-md-9">{{ $subscription->renewal_at?->format('Y-m-d') ?: __('N/A') }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('crm::subscription.fields.auto_renew') }}</div>
                <div class="col-md-9">{{ $subscription->auto_renew ? __('Yes') : __('No') }}</div>
            </div>
            <div class="row mb-0">
                <div class="col-md-3 fw-bold">{{ __('crm::subscription.fields.notes') }}</div>
                <div class="col-md-9">{{ $subscription->notes ?: __('N/A') }}</div>
            </div>
        </div>
    </div>

    <div class="card mt-6">
        <div class="card-header">
            <h3 class="card-title">{{ __('crm::subscription.sections.invoices') }}</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-row-dashed align-middle gs-0 gy-4">
                    <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th>{{ __('finance::invoice.fields.invoice_number') }}</th>
                        <th>{{ __('finance::invoice.fields.total') }}</th>
                        <th>{{ __('finance::invoice.fields.status') }}</th>
                        <th>{{ __('finance::invoice.fields.issued_at') }}</th>
                        <th>{{ __('finance::invoice.fields.due_at') }}</th>
                        <th class="text-end"></th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    @forelse($subscription->invoices as $invoice)
                        <tr>
                            <td>
                                <a href="{{ route('admin.finance.invoices.show', $invoice) }}" class="text-hover-primary fw-bold">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td>{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td>
                            <td>
                                @php
                                    $invoiceColor = match($invoice->status) {
                                        'paid' => 'success',
                                        'overdue' => 'danger',
                                        'sent' => 'primary',
                                        'void' => 'secondary',
                                        default => 'warning',
                                    };
                                @endphp
                                <span class="badge badge-light-{{ $invoiceColor }}">
                                    {{ __('finance::invoice.status.'.$invoice->status) }}
                                </span>
                            </td>
                            <td>{{ $invoice->issued_at?->format('Y-m-d') }}</td>
                            <td>{{ $invoice->due_at?->format('Y-m-d') ?: '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.finance.invoices.pdf', $invoice) }}"
                                   class="btn btn-sm btn-light-primary"
                                   title="{{ __('finance::invoice.actions.download_pdf') }}">
                                    <i class="bi bi-file-pdf me-1"></i>{{ __('finance::invoice.actions.download_pdf') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted text-center py-10">
                                <div class="mb-3">
                                    <i class="bi bi-receipt fs-2x text-gray-400"></i>
                                </div>
                                {{ __('crm::subscription.empty.invoices') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('crm::admin.partials.timeline', ['subject' => $subscription, 'subjectType' => 'subscription'])
</x-admin-layout>

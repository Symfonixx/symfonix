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
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.companies.show', $subscription->company_id) }}">
            <i class="bi bi-building me-1"></i>{{ $subscription->company?->name }}
        </a>
        <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.subscriptions.edit', $subscription->id) }}">
            <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $subscription->name }}</h3>
            <div class="card-toolbar">
                <span class="badge badge-light-{{ $statusColor }}">
                    {{ __('crm::subscription.status.'.$subscription->status) }}
                </span>
            </div>
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

    @include('crm::admin.partials.timeline', ['subject' => $subscription, 'subjectType' => 'subscription'])
</x-admin-layout>

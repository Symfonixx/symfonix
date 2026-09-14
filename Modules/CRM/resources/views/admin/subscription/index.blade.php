@section('title', __('crm::subscription.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::subscription.menu.subscriptions')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::subscription.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-warning"
           href="{{ route('admin.subscriptions.index', ['renewing_soon' => 1]) }}">
            <i class="bi bi-calendar-event me-1"></i>{{ __('crm::subscription.actions.renewing_soon') }}
        </a>
        <x-can perform="sales.subscriptions.create">
            <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.subscriptions.create') }}">
                {{ __('crm::subscription.actions.add') }} <i class="bi bi-plus-lg mx-1"></i>
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    <x-admin.table :model="$model" :search="__('crm::subscription.search.placeholder')" :formUrl="route('admin.subscriptions.deleteMulti')">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="w-10px pe-2" data-orderable="false">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                           data-kt-check-target="#dataTable .form-check-input" value="1"/>
                </div>
            </th>
            <th>{{ __('crm::subscription.fields.name') }}</th>
            <th>{{ __('crm::subscription.fields.company') }}</th>
            <th>{{ __('crm::subscription.fields.amount') }}</th>
            <th>{{ __('crm::subscription.fields.billing_cycle') }}</th>
            <th>{{ __('crm::subscription.fields.status') }}</th>
            <th>{{ __('crm::subscription.fields.renewal_at') }}</th>
            <th>{{ __('Created At') }}</th>
            <th class="text-end"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $subscription)
            @php
                $statusColor = match($subscription->status) {
                    'active' => 'success',
                    'trial' => 'info',
                    'paused' => 'warning',
                    'cancelled', 'expired' => 'danger',
                    default => 'secondary',
                };
                $renewalSoon = $subscription->renewal_at
                    && $subscription->renewal_at->between(now(), now()->addDays(30))
                    && in_array($subscription->status, ['active', 'trial']);
            @endphp
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="{{ $subscription->id }}"/>
                    </div>
                </td>
                <td>{{ $subscription->name }}</td>
                <td>
                    <a href="{{ route('admin.companies.show', $subscription->company_id) }}" class="text-hover-primary">
                        {{ $subscription->company?->name ?: __('N/A') }}
                    </a>
                </td>
                <td>{{ number_format($subscription->amount, 2) }} {{ $subscription->currency }}</td>
                <td>{{ __('crm::subscription.billing_cycle.'.$subscription->billing_cycle) }}</td>
                <td>
                    <span class="badge badge-light-{{ $statusColor }}">
                        {{ __('crm::subscription.status.'.$subscription->status) }}
                    </span>
                </td>
                <td>
                    @if($subscription->renewal_at)
                        <span class="{{ $renewalSoon ? 'text-warning fw-bold' : '' }}">
                            {{ $subscription->renewal_at->format('Y-m-d') }}
                        </span>
                    @else
                        {{ __('N/A') }}
                    @endif
                </td>
                <td>{{ $subscription->created_at->diffForHumans() }}</td>
                <td class="text-end">
                    <a href="{{ route('admin.subscriptions.show', $subscription->id) }}"
                       class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1">
                        <i class="bi bi-eye fs-5"></i>
                    </a>
                    <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}"
                       class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                        <i class="ki-duotone ki-message-edit fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>
                    <form class="d-inline" method="POST" action="{{ route('admin.subscriptions.destroy', $subscription->id) }}" data-confirm-delete>
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm">
                            <i class="bi bi-trash fs-5"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </x-admin.table>
</x-admin-layout>

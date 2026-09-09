@php
    $currency = $analytics['currency'] ?? 'USD';
    $rows = $analytics['top_customers'] ?? [];
@endphp

<div class="{{ $widget['span'] }} crm-widget" data-widget-id="{{ $widget['id'] }}">
    <div class="card crm-panel-card h-100">
        @include('crm::admin.dashboard.widgets._card_header', [
            'widget' => $widget,
            'title' => __('crm::dashboard.widgets.top_customers'),
            'subtitle' => __('crm::dashboard.widgets.top_customers_hint'),
        ])
        <div class="card-body pt-2">
            <div class="table-responsive">
                <table class="table table-row-dashed align-middle gy-4 mb-0">
                    <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase">
                        <th><i class="bi bi-building me-1"></i>{{ __('crm::dashboard.widgets.customer') }}</th>
                        <th><i class="bi bi-briefcase me-1"></i>{{ __('crm::dashboard.widgets.deals') }}</th>
                        <th><i class="bi bi-cash-stack me-1"></i>{{ __('crm::dashboard.widgets.value') }}</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                    @forelse($rows as $index => $customer)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="symbol symbol-35px symbol-circle">
                                        <span class="symbol-label bg-light-{{ $index === 0 ? 'warning' : 'primary' }} text-{{ $index === 0 ? 'warning' : 'primary' }} fw-bold">
                                            {{ strtoupper(substr($customer['name'], 0, 1)) }}
                                        </span>
                                    </span>
                                    <a href="{{ route('admin.companies.show', $customer['company_id']) }}" class="text-gray-800 text-hover-primary fw-semibold">
                                        {{ $customer['name'] }}
                                        @if($index === 0)
                                            <i class="bi bi-star-fill text-warning ms-1 fs-8"></i>
                                        @endif
                                    </a>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light-info">{{ $customer['deals_count'] }}</span>
                            </td>
                            <td class="fw-bold text-gray-900">{{ number_format($customer['total_value'], 0) }} {{ $currency }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-10">
                                <i class="bi bi-people fs-2 d-block mb-3 text-gray-400"></i>
                                {{ __('crm::dashboard.widgets.top_customers_empty') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

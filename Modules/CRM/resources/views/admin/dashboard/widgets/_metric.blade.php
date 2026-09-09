@php
    $data = $analytics['widgets'][$widget['id']] ?? [];
    $trend = (float) ($data['trend'] ?? 0);
    $trendUp = $trend >= 0;
    $currency = $data['currency'] ?? ($analytics['currency'] ?? 'USD');
    $showTrend = array_key_exists('trend', $data) && ! in_array($widget['id'], [
        'leads_in_progress', 'active_customers', 'lost_customers', 'total_sales',
        'avg_deal_value', 'avg_close_time', 'outstanding_invoices', 'overdue_amounts',
    ], true);
    $sparkline = $data['sparkline'] ?? [];
    $progress = (int) ($data['progress'] ?? ($widget['id'] === 'conversion_rate' ? ($data['value'] ?? 0) : 0));
    $color = $widget['color'] ?? 'primary';

    $value = match ($widget['id']) {
        'conversion_rate' => ($data['value'] ?? 0).'%',
        'avg_close_time' => __('crm::dashboard.metrics.days', ['value' => $data['value'] ?? 0]),
        'total_sales', 'sales_this_month', 'pipeline_value', 'avg_deal_value', 'overdue_amounts' => number_format((float) ($data['value'] ?? 0), 0).' '.$currency,
        'outstanding_invoices' => number_format((float) ($data['value'] ?? 0), 0).' '.$currency,
        'won_deals', 'lost_deals' => number_format((int) ($data['count'] ?? 0)),
        default => number_format((int) ($data['value'] ?? 0)),
    };

    $hint = match ($widget['id']) {
        'conversion_rate' => __('crm::dashboard.metrics.leads_converted', [
            'converted' => $data['converted'] ?? 0,
            'total' => $data['total'] ?? 0,
        ]),
        'won_deals' => __('crm::dashboard.metrics.won_value', [
            'count' => $data['count'] ?? 0,
            'value' => number_format((float) ($data['value'] ?? 0), 0).' '.$currency,
        ]),
        'lost_deals' => __('crm::dashboard.metrics.lost_value', [
            'count' => $data['count'] ?? 0,
            'value' => number_format((float) ($data['value'] ?? 0), 0).' '.$currency,
        ]),
        'outstanding_invoices' => __('crm::dashboard.metrics.invoice_count', [
            'count' => $data['count'] ?? 0,
        ]),
        'overdue_amounts' => __('crm::dashboard.metrics.invoice_count', [
            'count' => $data['count'] ?? 0,
        ]),
        default => $showTrend ? __('crm::dashboard.metrics.vs_previous') : null,
    };
@endphp

<div class="{{ $widget['span'] }} crm-widget" data-widget-id="{{ $widget['id'] }}">
    <div class="card crm-metric-card crm-metric-card--{{ $color }}">
        <div class="card-body p-6 position-relative overflow-hidden">
            <span class="crm-metric-watermark text-{{ $color }}">
                <i class="bi bi-{{ $widget['icon'] }}"></i>
            </span>
            <div class="d-flex align-items-start justify-content-between mb-4 position-relative">
                <div class="d-flex align-items-center gap-3">
                    <span class="crm-drag-handle text-muted" title="{{ __('crm::dashboard.customize.drag_hint') }}">
                        <i class="bi bi-grip-vertical fs-3"></i>
                    </span>
                    <div class="crm-metric-icon bg-light-{{ $color }} text-{{ $color }}">
                        <i class="bi bi-{{ $widget['icon'] }}"></i>
                    </div>
                </div>
                @if($showTrend)
                    <span class="badge badge-light-{{ $trendUp ? 'success' : 'danger' }} fw-bold">
                        <i class="bi bi-caret-{{ $trendUp ? 'up' : 'down' }}-fill me-1"></i>
                        {{ ($trendUp ? '+' : '').$trend }}%
                    </span>
                @endif
            </div>
            <div class="position-relative">
                <div class="fs-2hx fw-bold text-gray-900 lh-1 mb-2">{{ $value }}</div>
                <div class="text-gray-700 fw-semibold mb-1">{{ __('crm::dashboard.metrics.'.$widget['id']) }}</div>
                @if($hint)
                    <div class="text-muted fs-8 mb-4">{{ $hint }}</div>
                @else
                    <div class="mb-4"></div>
                @endif
                <div class="d-flex align-items-end justify-content-between gap-4">
                    @if(! empty($sparkline))
                        <div class="text-{{ $color }} flex-grow-1">
                            @include('crm::admin.dashboard.widgets._sparkline', ['series' => $sparkline])
                        </div>
                    @endif
                    @if($progress > 0)
                        <div class="crm-metric-progress flex-grow-1">
                            <div class="progress h-6px bg-light-{{ $color }}">
                                <div class="progress-bar bg-{{ $color }}" style="width: {{ min($progress, 100) }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

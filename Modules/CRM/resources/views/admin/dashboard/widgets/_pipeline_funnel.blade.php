@php
    $currency = $analytics['currency'] ?? 'USD';
@endphp

<div class="{{ $widget['span'] }} crm-widget" data-widget-id="{{ $widget['id'] }}">
    <div class="card crm-panel-card h-100">
        @include('crm::admin.dashboard.widgets._card_header', [
            'widget' => $widget,
            'title' => __('crm::dashboard.charts.pipeline_funnel'),
            'subtitle' => __('crm::dashboard.charts.pipeline_hint'),
        ])
        <div class="card-body pt-4">
            <div class="row g-6 align-items-center">
                <div class="col-lg-7">
                    @forelse($analytics['pipeline_funnel'] as $stage)
                        <div class="crm-funnel-row">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-semibold text-gray-800 d-flex align-items-center gap-2">
                                    <span class="crm-funnel-swatch bg-{{ $stage['color'] }}"></span>
                                    {{ $stage['name'] }}
                                </div>
                                <div class="text-muted fs-7">
                                    <span class="fw-bold text-gray-800">{{ $stage['count'] }}</span>
                                    @if($stage['value'] > 0)
                                        · {{ number_format($stage['value'], 0) }} {{ $currency }}
                                    @endif
                                </div>
                            </div>
                            <div class="crm-funnel-bar">
                                <span class="bg-{{ $stage['color'] }}" style="width: {{ max($stage['percentage'], 4) }}%"></span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No data available') }}</p>
                    @endforelse
                </div>
                <div class="col-lg-5">
                    <div class="crm-chart-box crm-chart-box--funnel">
                        <canvas id="crmPipelineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

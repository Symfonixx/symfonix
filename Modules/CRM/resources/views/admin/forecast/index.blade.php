@php
    $f = $forecast['filters'];
    $summary = $forecast['summary'];
    $currency = $forecast['currency'];
    $breadcrumbItems = [
        ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
        ['label' => __('crm::forecast.menu')],
    ];
@endphp

@section('title', __('crm::forecast.title'))

@section('toolbar')
    <x-admin.breadcrumb :pageTitle="__('crm::forecast.title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <div class="crm-dash-hero p-6 p-lg-8 mb-8">
        <div class="row g-6 align-items-center">
            <div class="col-xl-8">
                <span class="badge badge-light-primary mb-3">
                    <i class="bi bi-graph-up-arrow me-1"></i>{{ __('crm::forecast.menu') }}
                </span>
                <h2 class="text-white fw-bold fs-2x mb-2">{{ __('crm::forecast.title') }}</h2>
                <p class="text-white opacity-75 mb-0">{{ __('crm::forecast.subtitle') }}</p>
            </div>
            <div class="col-xl-4 text-xl-end">
                <span class="crm-hero-chip">
                    <i class="bi bi-calendar3 me-2"></i>{{ $f['date_from'] }} — {{ $f['date_to'] }}
                </span>
            </div>
        </div>
    </div>

    <div class="crm-filter-card card card-body mb-8">
        <form method="GET" action="{{ route('admin.crm.sales-forecasts.index') }}" class="row g-4 align-items-end">
            <div class="col-md-2">
                <label class="form-label fw-semibold">
                    <i class="bi bi-calendar-range text-primary me-1"></i>{{ __('crm::forecast.filters.from') }}
                </label>
                <input type="date" name="date_from" value="{{ $f['date_from'] }}" class="form-control form-control-solid"/>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">{{ __('crm::forecast.filters.to') }}</label>
                <input type="date" name="date_to" value="{{ $f['date_to'] }}" class="form-control form-control-solid"/>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    <i class="bi bi-person-badge text-info me-1"></i>{{ __('crm::forecast.filters.rep') }}
                </label>
                <select name="assigned_to" class="form-select form-select-solid" data-control="select2">
                    <option value="">{{ __('crm::forecast.filters.all_reps') }}</option>
                    @foreach($forecast['assignees'] as $assignee)
                        <option value="{{ $assignee->id }}" @selected((int) ($f['assigned_to'] ?? 0) === $assignee->id)>
                            {{ $assignee->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    <i class="bi bi-funnel text-success me-1"></i>{{ __('crm::forecast.filters.stage') }}
                </label>
                <select name="pipeline_stage_id" class="form-select form-select-solid" data-control="select2">
                    <option value="">{{ __('crm::forecast.filters.all_stages') }}</option>
                    @foreach($forecast['stages'] as $stage)
                        <option value="{{ $stage['id'] }}" @selected((int) ($f['pipeline_stage_id'] ?? 0) === $stage['id'])>
                            {{ $stage['name'] }} ({{ $stage['probability'] }}%)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i>{{ __('crm::forecast.filters.apply') }}
                </button>
            </div>
        </form>
    </div>

    <div class="row g-5 g-xl-8 mb-8">
        @php
            $metrics = [
                ['key' => 'total_pipeline', 'icon' => 'cash-stack', 'color' => 'primary', 'hint' => 'total_pipeline_hint'],
                ['key' => 'forecasted_revenue', 'icon' => 'graph-up', 'color' => 'success', 'hint' => 'forecasted_revenue_hint'],
                ['key' => 'best_case', 'icon' => 'arrow-up-circle', 'color' => 'info', 'hint' => 'best_case_hint'],
                ['key' => 'worst_case', 'icon' => 'arrow-down-circle', 'color' => 'warning', 'hint' => 'worst_case_hint'],
            ];
        @endphp
        @foreach($metrics as $metric)
            @php $data = $summary[$metric['key']]; @endphp
            <div class="col-sm-6 col-xl-3">
                <div class="card card-flush h-100 crm-metric-card">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-4">
                            <span class="crm-metric-icon bg-light-{{ $metric['color'] }} text-{{ $metric['color'] }}">
                                <i class="bi bi-{{ $metric['icon'] }}"></i>
                            </span>
                            <div class="ms-3">
                                <span class="text-muted fs-7 d-block">{{ __('crm::forecast.metrics.'.$metric['key']) }}</span>
                                <span class="fs-2 fw-bold text-gray-800">
                                    {{ number_format($data['value'], 0) }} {{ $currency }}
                                </span>
                            </div>
                        </div>
                        <span class="text-muted fs-8 mt-auto">{{ __('crm::forecast.metrics.'.$metric['hint']) }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-5 g-xl-8 mb-8">
        <div class="col-xl-7">
            <div class="card crm-panel-card h-100">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold">{{ __('crm::forecast.charts.pipeline_comparison') }}</h3>
                    <span class="text-muted fs-7">{{ __('crm::forecast.charts.pipeline_comparison_hint') }}</span>
                </div>
                <div class="card-body pt-2">
                    <div class="crm-chart-box" style="height: 360px;">
                        <canvas id="forecastStageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card crm-panel-card h-100">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title fw-bold">{{ __('crm::forecast.charts.stage_table') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 mb-0">
                            <thead>
                            <tr class="text-muted fw-bold fs-7">
                                <th>{{ __('crm::forecast.table.stage') }}</th>
                                <th class="text-end">{{ __('crm::forecast.table.deals') }}</th>
                                <th class="text-end">{{ __('crm::forecast.table.unweighted') }}</th>
                                <th class="text-end">{{ __('crm::forecast.table.weighted') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($forecast['stage_breakdown'] as $stage)
                                <tr>
                                    <td>
                                        <span class="crm-funnel-swatch bg-{{ $stage['color'] }}"></span>
                                        {{ $stage['name'] }}
                                    </td>
                                    <td class="text-end">{{ $stage['deal_count'] }}</td>
                                    <td class="text-end">{{ number_format($stage['unweighted_value'], 0) }}</td>
                                    <td class="text-end fw-semibold text-success">{{ number_format($stage['weighted_value'], 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-6">{{ __('No data available') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card crm-panel-card mb-8">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('crm::forecast.charts.monthly_predictions') }}</h3>
            <span class="text-muted fs-7">{{ __('crm::forecast.charts.monthly_predictions_hint') }}</span>
        </div>
        <div class="card-body pt-2">
            <div class="crm-chart-box" style="height: 380px;">
                <canvas id="forecastMonthlyChart"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const stageData = @json($forecast['stage_breakdown']);
            const monthlyData = @json($forecast['monthly_predictions']);
            const chartColors = @json($forecast['chart_colors']);
            const currency = @json($currency);
            const tooltipTheme = {
                backgroundColor: '#1e1e2d',
                titleColor: '#fff',
                bodyColor: '#a1a5b7',
                padding: 12,
                cornerRadius: 10,
                displayColors: true,
                boxPadding: 4,
            };

            function formatCurrency(value) {
                return new Intl.NumberFormat(undefined, { maximumFractionDigits: 0 }).format(value) + ' ' + currency;
            }

            function markChartUnavailable(canvas) {
                const box = canvas?.closest('.crm-chart-box');
                if (!box) return;
                box.innerHTML = '<div class="text-muted fs-7 text-center d-flex align-items-center justify-content-center h-100">{{ __('No data available') }}</div>';
            }

            if (typeof Chart === 'undefined') {
                document.querySelectorAll('#forecastStageChart, #forecastMonthlyChart').forEach(markChartUnavailable);
            } else {
                const stageCtx = document.getElementById('forecastStageChart');
                if (stageCtx && stageData.length) {
                    new Chart(stageCtx, {
                        type: 'bar',
                        data: {
                            labels: stageData.map(item => item.name),
                            datasets: [
                                {
                                    label: @json(__('crm::forecast.charts.unweighted')),
                                    data: stageData.map(item => item.unweighted_value),
                                    backgroundColor: chartColors[0],
                                    borderRadius: 8,
                                    maxBarThickness: 32,
                                },
                                {
                                    label: @json(__('crm::forecast.charts.weighted')),
                                    data: stageData.map(item => item.weighted_value),
                                    backgroundColor: chartColors[1],
                                    borderRadius: 8,
                                    maxBarThickness: 32,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top' },
                                tooltip: {
                                    ...tooltipTheme,
                                    callbacks: {
                                        label: (ctx) => ctx.dataset.label + ': ' + formatCurrency(ctx.raw),
                                    },
                                },
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: (value) => formatCurrency(value),
                                    },
                                    grid: { color: 'rgba(0,0,0,0.04)' },
                                },
                                x: { grid: { display: false } },
                            },
                        },
                    });
                } else if (stageCtx) {
                    markChartUnavailable(stageCtx);
                }

                const monthlyCtx = document.getElementById('forecastMonthlyChart');
                if (monthlyCtx && monthlyData.some(item => item.unweighted_value > 0 || item.weighted_value > 0)) {
                    new Chart(monthlyCtx, {
                        type: 'line',
                        data: {
                            labels: monthlyData.map(item => item.label),
                            datasets: [
                                {
                                    label: @json(__('crm::forecast.charts.unweighted')),
                                    data: monthlyData.map(item => item.unweighted_value),
                                    borderColor: chartColors[0],
                                    backgroundColor: chartColors[0] + '33',
                                    fill: true,
                                    tension: 0.35,
                                    pointRadius: 4,
                                },
                                {
                                    label: @json(__('crm::forecast.charts.weighted')),
                                    data: monthlyData.map(item => item.weighted_value),
                                    borderColor: chartColors[1],
                                    backgroundColor: chartColors[1] + '33',
                                    fill: true,
                                    tension: 0.35,
                                    pointRadius: 4,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { position: 'top' },
                                tooltip: {
                                    ...tooltipTheme,
                                    callbacks: {
                                        label: (ctx) => ctx.dataset.label + ': ' + formatCurrency(ctx.raw),
                                    },
                                },
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: (value) => formatCurrency(value),
                                    },
                                    grid: { color: 'rgba(0,0,0,0.04)' },
                                },
                                x: { grid: { display: false } },
                            },
                        },
                    });
                } else if (monthlyCtx) {
                    markChartUnavailable(monthlyCtx);
                }
            }
        </script>
    @endpush
</x-admin-layout>

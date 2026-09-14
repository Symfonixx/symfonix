@section('title', __('reporting::report.pages.sales_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('reporting::report.menu.reports')],
            ['label' => __('reporting::report.pages.sales_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('reporting::report.pages.sales_title')" :breadcrumbItems="$breadcrumbItems"/>
    @include('reporting::admin._export_buttons', ['department' => 'sales', 'filters' => $filters])
@endsection

<x-admin-layout>
    @include('reporting::admin._tabs', ['active' => 'sales'])
    @include('reporting::admin._filters', ['filters' => $filters, 'showAssignee' => true, 'assignees' => $report['assignees'] ?? collect()])
    @include('reporting::admin._kpi_cards', ['kpis' => $report['kpis'], 'currency' => $report['currency'] ?? null, 'columns' => 3])

    <div class="row g-5 mb-5">
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.pipeline_funnel') }}</h3></div>
                <div class="card-body">
                    @include('reporting::admin._chart_canvas', ['id' => 'chart-pipeline-funnel', 'height' => 280])
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.won_lost_trend') }}</h3></div>
                <div class="card-body">
                    @include('reporting::admin._chart_canvas', ['id' => 'chart-won-lost', 'height' => 280])
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 mb-5">
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.rep_performance') }}</h3></div>
                <div class="card-body">
                    @include('reporting::admin._chart_canvas', ['id' => 'chart-rep-performance', 'height' => 260])
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.stage_breakdown') }}</h3></div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-4">
                            <thead>
                            <tr class="text-muted fw-bold fs-7">
                                <th>{{ __('crm::deal.fields.stage') }}</th>
                                <th>{{ __('Count') }}</th>
                                <th>{{ __('Value') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($report['charts']['stage_breakdown'] as $row)
                                <tr>
                                    <td>{{ $row['stage'] }}</td>
                                    <td>{{ $row['count'] }}</td>
                                    <td class="fw-bold">{{ number_format($row['value'], 2) }} {{ $report['currency'] ?? '' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-6">{{ __('reporting::report.no_data') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        @include('reporting::admin._chart_js')
        <script>
        (function () {
            var colors = @json($report['chart_colors']);
            var funnel = @json($report['charts']['pipeline_funnel']);
            var wonLost = @json($report['charts']['won_lost_trend']);
            var reps = @json($report['charts']['rep_performance']);
            var renderFailure = function (canvas, message) {
                if (canvas && canvas.parentNode) {
                    canvas.parentNode.innerHTML = '<div class="text-danger text-center py-10">' + message + '</div>';
                }
            };

            if (typeof Chart === 'undefined') {
                var missing = document.querySelectorAll('canvas[id^="chart-"]');
                for (var i = 0; i < missing.length; i++) {
                    renderFailure(missing[i], 'Unable to load chart library');
                }
                return;
            }

            var funnelCanvas = document.getElementById('chart-pipeline-funnel');
            var wonLostCanvas = document.getElementById('chart-won-lost');
            var repsCanvas = document.getElementById('chart-rep-performance');

            if (funnelCanvas && Array.isArray(funnel) && funnel.length) {
                try {
                    new Chart(funnelCanvas, {
                        type: 'bar',
                        data: {
                            labels: funnel.map(function (r) { return r.name; }),
                            datasets: [{ data: funnel.map(function (r) { return r.count; }), backgroundColor: colors[0] }],
                        },
                        options: { indexAxis: 'y', scales: { x: { beginAtZero: true } } },
                    });
                } catch (error) {
                    renderFailure(funnelCanvas, 'Pipeline chart error: ' + (error && error.message ? error.message : 'unknown'));
                }
            } else {
                renderFailure(funnelCanvas, @json(__('reporting::report.no_data')));
            }

            if (wonLostCanvas && Array.isArray(wonLost) && wonLost.length) {
                try {
                    new Chart(wonLostCanvas, {
                        type: 'line',
                        data: {
                            labels: wonLost.map(function (r) { return r.label; }),
                            datasets: [
                                { label: '{{ __("Won") }}', data: wonLost.map(function (r) { return r.won; }), borderColor: colors[1], tension: 0.3 },
                                { label: '{{ __("Lost") }}', data: wonLost.map(function (r) { return r.lost; }), borderColor: colors[4], tension: 0.3 },
                            ],
                        },
                        options: { scales: { y: { beginAtZero: true } } },
                    });
                } catch (error) {
                    renderFailure(wonLostCanvas, 'Trend chart error: ' + (error && error.message ? error.message : 'unknown'));
                }
            } else {
                renderFailure(wonLostCanvas, @json(__('reporting::report.no_data')));
            }

            if (repsCanvas && Array.isArray(reps) && reps.length) {
                try {
                    new Chart(repsCanvas, {
                        type: 'bar',
                        data: {
                            labels: reps.map(function (r) { return r.name; }),
                            datasets: [{ label: '{{ __("reporting::report.kpis.won_deals") }}', data: reps.map(function (r) { return r.won_value; }), backgroundColor: colors[3] }],
                        },
                        options: { scales: { y: { beginAtZero: true } } },
                    });
                } catch (error) {
                    renderFailure(repsCanvas, 'Rep chart error: ' + (error && error.message ? error.message : 'unknown'));
                }
            } else {
                renderFailure(repsCanvas, @json(__('reporting::report.no_data')));
            }
        })();
        </script>
    @endpush
</x-admin-layout>

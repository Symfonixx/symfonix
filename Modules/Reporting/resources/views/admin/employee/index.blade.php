@section('title', __('reporting::report.pages.employee_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('reporting::report.menu.reports')],
            ['label' => __('reporting::report.pages.employee_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('reporting::report.pages.employee_title')" :breadcrumbItems="$breadcrumbItems"/>
    @include('reporting::admin._export_buttons', ['department' => 'employee', 'filters' => $filters])
@endsection

<x-admin-layout>
    @include('reporting::admin._tabs', ['active' => 'employee'])
    @include('reporting::admin._filters', ['filters' => $filters, 'showEmployee' => true, 'employees' => $report['employees'] ?? collect()])
    @include('reporting::admin._kpi_cards', ['kpis' => $report['kpis'], 'columns' => 3])

    <div class="row g-5 mb-5">
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.attendance_trend') }}</h3></div>
                <div class="card-body"><canvas id="chart-attendance-trend" height="280"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.punch_types') }}</h3></div>
                <div class="card-body"><canvas id="chart-punch-types" height="280"></canvas></div>
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.employee_project_performance') }}</h3></div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed align-middle gs-0 gy-4">
                    <thead>
                    <tr class="text-muted fw-bold fs-7">
                        <th>{{ __('Employee') }}</th>
                        <th>{{ __('Project') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Start Date') }}</th>
                        <th>{{ __('End Date') }}</th>
                        <th>{{ __('Days Worked') }}</th>
                        <th>{{ __('Status') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($report['tables']['employee_project_performance'] as $row)
                        <tr>
                            <td>
                                @if(!empty($row['employee_id']))
                                    <a href="{{ route('admin.reporting.employee.show', ['employee' => $row['employee_id'], 'period' => $filters['period'] ?? 'this_month', 'date_from' => $filters['date_from'] ?? null, 'date_to' => $filters['date_to'] ?? null]) }}"
                                       class="text-primary fw-semibold">
                                        {{ $row['employee'] ?? '—' }}
                                    </a>
                                @else
                                    {{ $row['employee'] ?? '—' }}
                                @endif
                            </td>
                            <td>{{ $row['project'] ?? '—' }}</td>
                            <td>{{ $row['role'] ?? '—' }}</td>
                            <td>{{ $row['started_at'] ?? '—' }}</td>
                            <td>{{ $row['ended_at'] ?? '—' }}</td>
                            <td class="fw-bold">{{ $row['days_worked'] ?? 0 }}</td>
                            <td>{{ $row['project_status'] ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-6">{{ __('reporting::report.no_data') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>

@push('scripts')
<script>
(function () {
    var colors = @json($report['chart_colors']);
    var attendanceTrend = @json($report['charts']['attendance_trend']);
    var punchTypes = @json($report['charts']['punch_types']);
    var chartJsPromise = null;

    var ensureChartJs = function () {
        if (typeof Chart !== 'undefined') {
            return Promise.resolve();
        }

        if (!chartJsPromise) {
            chartJsPromise = new Promise(function (resolve, reject) {
                var sources = [
                    'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
                    'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js',
                    'https://unpkg.com/chart.js@4.4.1/dist/chart.umd.min.js'
                ];
                var tryLoad = function (index) {
                    if (index >= sources.length) {
                        reject(new Error('Chart.js failed to load'));
                        return;
                    }

                    var script = document.createElement('script');
                    script.src = sources[index];
                    script.async = true;
                    script.onload = function () { resolve(); };
                    script.onerror = function () { tryLoad(index + 1); };
                    document.head.appendChild(script);
                };

                tryLoad(0);
            });
        }

        return chartJsPromise;
    };

    var renderFailure = function (canvas, message) {
        if (canvas && canvas.parentNode) {
            canvas.parentNode.innerHTML = '<div class="text-danger text-center py-10">' + message + '</div>';
        }
    };

    var renderCharts = function () {
        var trendCanvas = document.getElementById('chart-attendance-trend');
        var punchCanvas = document.getElementById('chart-punch-types');

        if (trendCanvas && Array.isArray(attendanceTrend) && attendanceTrend.length) {
            try {
                new Chart(trendCanvas, {
                    type: 'line',
                    data: {
                        labels: attendanceTrend.map(function (r) { return r.label; }),
                        datasets: [
                            { label: '{{ __("Check-ins") }}', data: attendanceTrend.map(function (r) { return r.check_ins; }), borderColor: colors[1], tension: 0.3 },
                            { label: '{{ __("Check-outs") }}', data: attendanceTrend.map(function (r) { return r.check_outs; }), borderColor: colors[4], tension: 0.3 },
                        ],
                    },
                    options: { responsive: true, scales: { y: { beginAtZero: true } } },
                });
            } catch (error) {
                renderFailure(trendCanvas, 'Attendance chart error: ' + (error && error.message ? error.message : 'unknown'));
            }
        } else {
            renderFailure(trendCanvas, @json(__('reporting::report.no_data')));
        }

        if (punchCanvas && Array.isArray(punchTypes) && punchTypes.length) {
            try {
                new Chart(punchCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: punchTypes.map(function (r) { return r.type; }),
                        datasets: [{ data: punchTypes.map(function (r) { return r.count; }), backgroundColor: colors }],
                    },
                    options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
                });
            } catch (error) {
                renderFailure(punchCanvas, 'Punch chart error: ' + (error && error.message ? error.message : 'unknown'));
            }
        } else {
            renderFailure(punchCanvas, @json(__('reporting::report.no_data')));
        }
    };

    ensureChartJs().then(renderCharts).catch(function () {
        var chartHolders = document.querySelectorAll('canvas[id^="chart-"]');
        for (var i = 0; i < chartHolders.length; i++) {
            if (chartHolders[i] && chartHolders[i].parentNode) {
                chartHolders[i].parentNode.innerHTML = '<div class="text-danger text-center py-10">Unable to load chart library</div>';
            }
        }
    });
})();
</script>
@endpush


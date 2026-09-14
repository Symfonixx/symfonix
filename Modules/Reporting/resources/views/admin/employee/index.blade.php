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
                <div class="card-body">
                    @include('reporting::admin._chart_canvas', ['id' => 'chart-attendance-trend', 'height' => 280])
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.punch_types') }}</h3></div>
                <div class="card-body">
                    @include('reporting::admin._chart_canvas', ['id' => 'chart-punch-types', 'height' => 280])
                </div>
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
    @push('scripts')
        @include('reporting::admin._chart_js')
        <script>
        (function () {
            var colors = @json($report['chart_colors']);
            var attendanceTrend = @json($report['charts']['attendance_trend']);
            var punchTypes = @json($report['charts']['punch_types']);
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
                        options: { scales: { y: { beginAtZero: true } } },
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
                        options: { plugins: { legend: { position: 'bottom' } } },
                    });
                } catch (error) {
                    renderFailure(punchCanvas, 'Punch chart error: ' + (error && error.message ? error.message : 'unknown'));
                }
            } else {
                renderFailure(punchCanvas, @json(__('reporting::report.no_data')));
            }
        })();
        </script>
    @endpush
</x-admin-layout>


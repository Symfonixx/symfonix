@section('title', __('reporting::report.pages.operations_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('reporting::report.menu.reports')],
            ['label' => __('reporting::report.pages.operations_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('reporting::report.pages.operations_title')" :breadcrumbItems="$breadcrumbItems"/>
    @include('reporting::admin._export_buttons', ['department' => 'operations', 'filters' => $filters])
@endsection

<x-admin-layout>
    @include('reporting::admin._tabs', ['active' => 'operations'])
    @include('reporting::admin._filters', ['filters' => $filters])
    @include('reporting::admin._kpi_cards', ['kpis' => $report['kpis'], 'columns' => 3])

    <div class="row g-5 mb-5">
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.ticket_trend') }}</h3></div>
                <div class="card-body">
                    @include('reporting::admin._chart_canvas', ['id' => 'chart-ticket-trend', 'height' => 280])
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.ticket_status') }}</h3></div>
                <div class="card-body">
                    @include('reporting::admin._chart_canvas', ['id' => 'chart-ticket-status', 'height' => 280])
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 mb-5">
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.project_status') }}</h3></div>
                <div class="card-body">
                    @include('reporting::admin._chart_canvas', ['id' => 'chart-project-status', 'height' => 260])
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.attendance_daily') }}</h3></div>
                <div class="card-body">
                    @include('reporting::admin._chart_canvas', ['id' => 'chart-attendance', 'height' => 260])
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header"><h3 class="card-title">{{ __('support::ticket.menu.tickets') }}</h3></div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed align-middle gs-0 gy-4">
                    <thead>
                    <tr class="text-muted fw-bold fs-7">
                        <th>#</th>
                        <th>{{ __('Subject') }}</th>
                        <th>{{ __('Priority') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Date') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($report['tables']['open_tickets'] as $ticket)
                        <tr>
                            <td>{{ $ticket['number'] }}</td>
                            <td>{{ $ticket['subject'] }}</td>
                            <td><span class="badge badge-light">{{ $ticket['priority'] }}</span></td>
                            <td><span class="badge badge-light-warning">{{ $ticket['status'] }}</span></td>
                            <td>{{ $ticket['category'] ?? '—' }}</td>
                            <td>{{ $ticket['created_at'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-6">{{ __('reporting::report.no_data') }}</td></tr>
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
            var trend = @json($report['charts']['ticket_trend']);
            var status = @json($report['charts']['ticket_status']);
            var projects = @json($report['charts']['project_status']);
            var attendance = @json($report['charts']['attendance_daily']);
            var noDataText = @json(__('reporting::report.no_data'));
            var showMessage = function (canvas, message, danger) {
                if (!canvas || !canvas.parentNode) {
                    return;
                }
                canvas.parentNode.innerHTML = '<div class="' + (danger ? 'text-danger' : 'text-muted') + ' text-center py-10">' + message + '</div>';
            };

            if (typeof Chart === 'undefined') {
                var missing = document.querySelectorAll('canvas[id^="chart-"]');
                for (var i = 0; i < missing.length; i++) {
                    showMessage(missing[i], 'Unable to load chart library', true);
                }
                return;
            }

            var trendCanvas = document.getElementById('chart-ticket-trend');
            var statusCanvas = document.getElementById('chart-ticket-status');
            var projectsCanvas = document.getElementById('chart-project-status');
            var attendanceCanvas = document.getElementById('chart-attendance');

            if (trendCanvas && Array.isArray(trend) && trend.length) {
                new Chart(trendCanvas, {
                    type: 'line',
                    data: {
                        labels: trend.map(function (r) { return r.label; }),
                        datasets: [
                            { label: '{{ __("Opened") }}', data: trend.map(function (r) { return r.opened; }), borderColor: colors[4], tension: 0.3 },
                            { label: '{{ __("Resolved") }}', data: trend.map(function (r) { return r.resolved; }), borderColor: colors[1], tension: 0.3 },
                        ],
                    },
                    options: { scales: { y: { beginAtZero: true } } },
                });
            } else {
                showMessage(trendCanvas, noDataText, false);
            }

            if (statusCanvas && Array.isArray(status) && status.length) {
                new Chart(statusCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: status.map(function (r) { return r.status; }),
                        datasets: [{ data: status.map(function (r) { return r.count; }), backgroundColor: colors }],
                    },
                    options: { plugins: { legend: { position: 'bottom' } } },
                });
            } else {
                showMessage(statusCanvas, noDataText, false);
            }

            if (projectsCanvas && Array.isArray(projects) && projects.length) {
                new Chart(projectsCanvas, {
                    type: 'bar',
                    data: {
                        labels: projects.map(function (r) { return r.status; }),
                        datasets: [{ data: projects.map(function (r) { return r.count; }), backgroundColor: colors[0] }],
                    },
                    options: { scales: { y: { beginAtZero: true } } },
                });
            } else {
                showMessage(projectsCanvas, noDataText, false);
            }

            if (attendanceCanvas && Array.isArray(attendance) && attendance.length) {
                new Chart(attendanceCanvas, {
                    type: 'line',
                    data: {
                        labels: attendance.map(function (r) { return r.label; }),
                        datasets: [{ label: '{{ __("Check-ins") }}', data: attendance.map(function (r) { return r.check_ins; }), borderColor: colors[3], tension: 0.3, fill: true, backgroundColor: colors[3] + '22' }],
                    },
                    options: { scales: { y: { beginAtZero: true } } },
                });
            } else {
                showMessage(attendanceCanvas, noDataText, false);
            }
        })();
        </script>
    @endpush
</x-admin-layout>

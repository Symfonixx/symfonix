@section('title', __('reporting::report.pages.employee_profile_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('reporting::report.menu.reports')],
            ['label' => __('reporting::report.pages.employee_title'), 'url' => route('admin.reporting.employee')],
            ['label' => $employee->name],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="$employee->name" :breadcrumbItems="$breadcrumbItems"/>
    @include('reporting::admin._export_buttons', ['department' => 'employee', 'filters' => array_merge($filters, ['employee_id' => $employee->id])])
@endsection

<x-admin-layout>
    @include('reporting::admin._tabs', ['active' => 'employee'])
    @include('reporting::admin._filters', ['filters' => $filters])

    <div class="card mb-5">
        <div class="card-body d-flex flex-wrap align-items-center gap-6">
            <div>
                <div class="fs-3 fw-bold">{{ $employee->name }}</div>
                <div class="text-muted">{{ $employee->email ?: '—' }} {{ $employee->mobile ? ' • '.$employee->mobile : '' }}</div>
                <div class="mt-2">
                    <span class="badge badge-light-{{ ($report['summary']['fingerprint_enrolled'] ?? false) ? 'success' : 'warning' }}">
                        {{ ($report['summary']['fingerprint_enrolled'] ?? false) ? __('user::fingerprint.status.enrolled') : __('user::fingerprint.status.not_enrolled') }}
                    </span>
                </div>
            </div>
            <div class="ms-auto text-end">
                <div class="text-muted fs-7">{{ __('reporting::report.employee.last_attendance') }}</div>
                <div class="fw-semibold">{{ $report['summary']['last_attendance_at'] ?? '—' }}</div>
                <div class="text-muted fs-8 mt-2">{{ __('reporting::report.employee.active_assignments') }}: {{ $report['summary']['active_assignments'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    @include('reporting::admin._kpi_cards', ['kpis' => $report['kpis'], 'columns' => 3])

    <div class="row g-5 mb-5">
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.attendance_trend') }}</h3></div>
                <div class="card-body"><canvas id="chart-employee-attendance" height="260"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.salary_trend') }}</h3></div>
                <div class="card-body"><canvas id="chart-employee-salary" height="260"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row g-5 mb-5">
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.punch_types') }}</h3></div>
                <div class="card-body"><canvas id="chart-employee-punch-types" height="260"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('reporting::report.sections.project_status') }}</h3></div>
                <div class="card-body"><canvas id="chart-employee-project-status" height="260"></canvas></div>
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
                        <th>{{ __('Project') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Start Date') }}</th>
                        <th>{{ __('End Date') }}</th>
                        <th>{{ __('Days Worked') }}</th>
                        <th>{{ __('Status') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($report['tables']['project_assignments'] as $row)
                        <tr>
                            <td>
                                @if(!empty($row['project_id']))
                                    <a href="{{ route('admin.projects.show', $row['project_id']) }}" class="text-primary fw-semibold">{{ $row['project'] }}</a>
                                @else
                                    {{ $row['project'] ?? '—' }}
                                @endif
                            </td>
                            <td>{{ $row['role'] ?? '—' }}</td>
                            <td>{{ $row['started_at'] ?? '—' }}</td>
                            <td>{{ $row['ended_at'] ?? '—' }}</td>
                            <td class="fw-bold">{{ $row['days_worked'] ?? 0 }}</td>
                            <td>{{ $row['project_status'] ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-6">{{ __('reporting::report.no_data') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row g-5">
        <div class="col-lg-6">
            <div class="card mb-5">
                <div class="card-header"><h3 class="card-title">{{ __('Salary History') }}</h3></div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-4">
                            <thead>
                            <tr class="text-muted fw-bold fs-7">
                                <th>{{ __('Period') }}</th>
                                <th>{{ __('Salary') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Paid At') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($report['tables']['salary_history'] as $row)
                                <tr>
                                    <td>{{ $row['period'] ?? '—' }}</td>
                                    <td class="fw-bold">{{ number_format((float) ($row['base_salary'] ?? 0), 2) }}</td>
                                    <td>{{ $row['status'] ?? '—' }}</td>
                                    <td>{{ $row['paid_at'] ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-6">{{ __('reporting::report.no_data') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-5">
                <div class="card-header"><h3 class="card-title">{{ __('user::fingerprint.recent_attendance') }}</h3></div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-4">
                            <thead>
                            <tr class="text-muted fw-bold fs-7">
                                <th>{{ __('user::fingerprint.columns.recorded_at') }}</th>
                                <th>{{ __('user::fingerprint.columns.punch') }}</th>
                                <th>{{ __('user::fingerprint.columns.verify') }}</th>
                                <th>{{ __('user::fingerprint.columns.device_user_id') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($report['tables']['attendance_logs'] as $row)
                                <tr>
                                    <td>{{ $row['recorded_at'] ?? '—' }}</td>
                                    <td>{{ $row['punch_state'] ?? '—' }}</td>
                                    <td>{{ $row['verify_mode'] ?? '—' }}</td>
                                    <td>{{ $row['device_user_id'] ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-6">{{ __('reporting::report.no_data') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

@push('scripts')
<script>
(function () {
    var colors = @json($report['chart_colors']);
    var attendanceTrend = @json($report['charts']['attendance_trend']);
    var salaryTrend = @json($report['charts']['salary_trend']);
    var punchTypes = @json($report['charts']['punch_types']);
    var projectStatus = @json($report['charts']['project_status_breakdown']);
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

    var draw = function () {
        if (attendanceTrend.length && document.getElementById('chart-employee-attendance')) {
            new Chart(document.getElementById('chart-employee-attendance'), {
                type: 'line',
                data: {
                    labels: attendanceTrend.map(function (row) { return row.label; }),
                    datasets: [
                        { label: '{{ __("Check-ins") }}', data: attendanceTrend.map(function (row) { return row.check_ins; }), borderColor: colors[1], tension: 0.25 },
                        { label: '{{ __("Check-outs") }}', data: attendanceTrend.map(function (row) { return row.check_outs; }), borderColor: colors[4], tension: 0.25 },
                    ],
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } } },
            });
        }

        if (salaryTrend.length && document.getElementById('chart-employee-salary')) {
            new Chart(document.getElementById('chart-employee-salary'), {
                type: 'bar',
                data: {
                    labels: salaryTrend.map(function (row) { return row.label; }),
                    datasets: [
                        { label: '{{ __("Paid") }}', data: salaryTrend.map(function (row) { return row.paid; }), backgroundColor: colors[1] },
                        { label: '{{ __("Pending") }}', data: salaryTrend.map(function (row) { return row.pending; }), backgroundColor: colors[4] },
                    ],
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } } },
            });
        }

        if (punchTypes.length && document.getElementById('chart-employee-punch-types')) {
            new Chart(document.getElementById('chart-employee-punch-types'), {
                type: 'doughnut',
                data: {
                    labels: punchTypes.map(function (row) { return row.type; }),
                    datasets: [{ data: punchTypes.map(function (row) { return row.count; }), backgroundColor: colors }],
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
            });
        }

        if (projectStatus.length && document.getElementById('chart-employee-project-status')) {
            new Chart(document.getElementById('chart-employee-project-status'), {
                type: 'pie',
                data: {
                    labels: projectStatus.map(function (row) { return row.status; }),
                    datasets: [{ data: projectStatus.map(function (row) { return row.count; }), backgroundColor: colors }],
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
            });
        }
    };

    ensureChartJs().then(draw).catch(function () {});
})();
</script>
@endpush


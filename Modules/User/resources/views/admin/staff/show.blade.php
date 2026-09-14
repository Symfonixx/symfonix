@section('title', __('Employee Profile'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('Employees'), 'url' => route('admin.employees.index')],
            ['label' => $employee->name],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="$employee->name" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.employees.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to List') }}
        </a>
        @if(\Illuminate\Support\Facades\Route::has('admin.reporting.employee.show'))
            <a class="btn btn-sm fw-bold btn-light-info" href="{{ route('admin.reporting.employee.show', ['employee' => $employee->id]) }}">
                <i class="bi bi-graph-up-arrow me-1"></i>{{ __('Open Employee Report') }}
            </a>
        @endif
        <a class="btn btn-sm fw-bold btn-primary" data-bs-toggle="modal" data-bs-target="#edit_modal{{ $employee->id }}">
            <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
        </a>
        @if(! $employee->isAdminAccount())
            <x-can perform="hr.admins.create">
                <button type="button" class="btn btn-sm fw-bold btn-light-warning" data-bs-toggle="modal" data-bs-target="#convert_admin_modal">
                    <i class="bi bi-shield-lock me-1"></i>{{ __('Convert to Admin') }}
                </button>
            </x-can>
        @endif
        @if($employee->isOnWebsiteTeam())
            @can('cms.team.edit')
                <a class="btn btn-sm fw-bold btn-light-info" href="{{ route('admin.teams.edit', $employee->team) }}">
                    <i class="bi bi-pencil-square me-1"></i>{{ __('Edit Our Team') }}
                </a>
            @endcan
        @else
            <x-can perform="cms.team.create">
                <form method="POST"
                      action="{{ route('admin.employees.add-to-team', $employee) }}"
                      class="d-inline"
                      data-confirm="{{ __('user::emails.team.confirm', ['name' => $employee->name]) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm fw-bold btn-success">
                        <i class="bi bi-people me-1"></i>{{ __('Add to Our Team') }}
                    </button>
                </form>
            </x-can>
        @endif
    </div>
@endsection

<x-admin-layout>
    <div class="modal fade" tabindex="-1" id="edit_modal{{ $employee->id }}">
        @include('user::admin.staff._edit_model', ['employee' => $employee])
    </div>
    @if(! $employee->isAdminAccount())
        <x-can perform="hr.admins.create">
            <div class="modal fade" tabindex="-1" id="convert_admin_modal">
                @include('user::admin.staff._convert_to_admin_modal', ['convertEmployee' => $employee, 'groups' => $groups])
            </div>
        </x-can>
    @endif
    <div class="row g-5 mb-5">
        <div class="col-xl-4">
            <div class="card card-flush h-100">
                <div class="card-body text-center pt-10 pb-6">
                    <div class="symbol symbol-100px symbol-circle mb-5 mx-auto">
                        <img src="{{ $employee->avatar }}" alt="{{ $employee->name }}"/>
                    </div>
                    <h2 class="fs-2 fw-bold mb-1">{{ $employee->name }}</h2>
                    @if($employee->position)
                        <div class="text-gray-600 fw-semibold mb-2">{{ $employee->position }}</div>
                    @endif
                    <a href="mailto:{{ $employee->email }}" class="text-muted text-hover-primary d-block mb-3">{{ $employee->email }}</a>
                    @if($employee->mobile)
                        <a href="tel:{{ $employee->mobile }}" class="text-muted text-hover-primary d-block mb-3">{{ $employee->mobile }}</a>
                    @endif
                    <div class="d-flex justify-content-center gap-3 mb-4">
                        <span class="badge badge-light-{{ $employee->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($employee->status) }}</span>
                        <span class="badge badge-light-{{ $employee->isFingerprintEnrolled() ? 'success' : 'warning' }}">
                            {{ $employee->isFingerprintEnrolled() ? __('user::fingerprint.status.enrolled') : __('user::fingerprint.status.pending') }}
                        </span>
                        @if($employee->isAdminAccount())
                            <span class="badge badge-light-primary">{{ __('Admin') }}</span>
                        @endif
                        @if($employee->isOnWebsiteTeam())
                            <span class="badge badge-light-success">{{ __('Our Team') }}</span>
                        @endif
                    </div>
                    @if($employee->resumeUrl())
                        <a href="{{ $employee->resumeUrl() }}" target="_blank" class="btn btn-sm btn-light-success">
                            <i class="bi bi-file-earmark-arrow-down me-1"></i>{{ __('Download Resume') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="row g-5">
                <div class="col-md-4">
                    <div class="card card-flush h-100">
                        <div class="card-body">
                            <div class="text-muted fs-7">{{ __('Assigned Projects') }}</div>
                            <div class="fs-2hx fw-bold mt-2">{{ $stats['projects_assigned'] }}</div>
                            <div class="text-muted fs-8">{{ __('Active:') }} {{ $stats['active_assignments'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-flush h-100">
                        <div class="card-body">
                            <div class="text-muted fs-7">{{ __('Attendance') }}</div>
                            <div class="fs-2hx fw-bold mt-2">{{ $stats['attendance_days'] }}</div>
                            <div class="text-muted fs-8">{{ __('Events:') }} {{ $stats['attendance_events'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-flush h-100">
                        <div class="card-body">
                            <div class="text-muted fs-7">{{ __('Completed Projects') }}</div>
                            <div class="fs-2hx fw-bold mt-2">{{ $stats['completed_projects'] }}</div>
                            <div class="text-muted fs-8">{{ __('Employee performance') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-flush h-100">
                        <div class="card-body">
                            <div class="text-muted fs-7">{{ __('Paid Salary Total') }}</div>
                            <div class="fs-2 fw-bold mt-2">{{ number_format((float) $stats['paid_salary'], 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-flush h-100">
                        <div class="card-body">
                            <div class="text-muted fs-7">{{ __('Pending Salary Total') }}</div>
                            <div class="fs-2 fw-bold mt-2">{{ number_format((float) $stats['pending_salary'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 mb-5">
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('Attendance Trend (Last 30 Days)') }}</h3></div>
                <div class="card-body"><canvas id="chart-attendance-trend" height="260"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('Salary Trend') }}</h3></div>
                <div class="card-body"><canvas id="chart-salary-trend" height="260"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row g-5 mb-5">
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('Punch Types') }}</h3></div>
                <div class="card-body"><canvas id="chart-punch-types" height="240"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-flush h-100">
                <div class="card-header"><h3 class="card-title">{{ __('Project Status Distribution') }}</h3></div>
                <div class="card-body"><canvas id="chart-project-status" height="240"></canvas></div>
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header"><h3 class="card-title">{{ __('Project Assignments') }}</h3></div>
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
                    @forelse($assignments as $assignment)
                        <tr>
                            <td>
                                @if($assignment->project)
                                    <a href="{{ route('admin.projects.show', $assignment->project->id) }}" class="text-primary fw-semibold">
                                        {{ $assignment->project->title }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $assignment->role ?? '—' }}</td>
                            <td>{{ $assignment->started_at?->toDateString() ?? '—' }}</td>
                            <td>{{ $assignment->ended_at?->toDateString() ?? '—' }}</td>
                            <td class="fw-bold">{{ $assignment->daysWorked() }}</td>
                            <td>{{ $assignment->project?->status?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-6">{{ __('No project assignments yet.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($assignments->hasPages())
                <div class="mt-4">{{ $assignments->links() }}</div>
            @endif
        </div>
    </div>

    <div class="card">
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
                    @forelse($recentLogs as $log)
                        <tr>
                            <td>{{ $log->recorded_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td>{{ $log->punchStateLabel() }}</td>
                            <td>{{ $log->verifyModeLabel() ?? '—' }}</td>
                            <td>{{ $log->device_user_id ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-6">{{ __('No attendance logs yet.') }}</td></tr>
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
    var convertModal = document.getElementById('convert_admin_modal');
    if (convertModal) {
        var form = convertModal.querySelector('.js-convert-admin-form');
        if (form) {
            var selectAll = form.querySelector('.js-permission-select-all');
            var boxes = form.querySelectorAll('.js-permission-box');
            var sections = form.querySelectorAll('.js-permission-section');
            var syncSections = function () {
                sections.forEach(function (section) {
                    var key = section.getAttribute('data-section');
                    var related = form.querySelectorAll('.js-permission-box[data-section="' + key + '"]');
                    section.checked = related.length > 0 && Array.from(related).every(function (box) { return box.checked; });
                });
                if (selectAll) {
                    selectAll.checked = boxes.length > 0 && Array.from(boxes).every(function (box) { return box.checked; });
                }
            };
            if (selectAll) {
                selectAll.addEventListener('change', function (event) {
                    boxes.forEach(function (box) { box.checked = event.target.checked; });
                    sections.forEach(function (section) { section.checked = event.target.checked; });
                });
            }
            sections.forEach(function (section) {
                section.addEventListener('change', function (event) {
                    var key = section.getAttribute('data-section');
                    form.querySelectorAll('.js-permission-box[data-section="' + key + '"]').forEach(function (box) {
                        box.checked = event.target.checked;
                    });
                    syncSections();
                });
            });
            boxes.forEach(function (box) { box.addEventListener('change', syncSections); });
            syncSections();
        }
        @if($errors->any() && old('form_context') === 'convert_to_admin')
        if (typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(convertModal).show();
        }
        @endif
    }

    var attendanceTrend = @json($charts['attendance_trend']);
    var salaryTrend = @json($charts['salary_trend']);
    var punchTypes = @json($charts['punch_types']);
    var projectStatus = @json($charts['project_status']);
    var palette = ['#50CD89', '#009EF7', '#F1416C', '#FFC700', '#7239EA', '#181C32'];
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

    var render = function () {
        if (attendanceTrend.length && document.getElementById('chart-attendance-trend')) {
            new Chart(document.getElementById('chart-attendance-trend'), {
                type: 'line',
                data: {
                    labels: attendanceTrend.map(function (row) { return row.label; }),
                    datasets: [
                        { label: '{{ __("Check-ins") }}', data: attendanceTrend.map(function (row) { return row.check_ins; }), borderColor: palette[1], tension: 0.25 },
                        { label: '{{ __("Check-outs") }}', data: attendanceTrend.map(function (row) { return row.check_outs; }), borderColor: palette[2], tension: 0.25 }
                    ]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });
        }

        if (salaryTrend.length && document.getElementById('chart-salary-trend')) {
            new Chart(document.getElementById('chart-salary-trend'), {
                type: 'bar',
                data: {
                    labels: salaryTrend.map(function (row) { return row.label; }),
                    datasets: [
                        { label: '{{ __("Paid") }}', data: salaryTrend.map(function (row) { return row.paid; }), backgroundColor: palette[0] },
                        { label: '{{ __("Pending") }}', data: salaryTrend.map(function (row) { return row.pending; }), backgroundColor: palette[2] }
                    ]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });
        }

        if (punchTypes.length && document.getElementById('chart-punch-types')) {
            new Chart(document.getElementById('chart-punch-types'), {
                type: 'doughnut',
                data: {
                    labels: punchTypes.map(function (row) { return row.type; }),
                    datasets: [{ data: punchTypes.map(function (row) { return row.count; }), backgroundColor: palette }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });
        }

        if (projectStatus.length && document.getElementById('chart-project-status')) {
            new Chart(document.getElementById('chart-project-status'), {
                type: 'pie',
                data: {
                    labels: projectStatus.map(function (row) { return row.status; }),
                    datasets: [{ data: projectStatus.map(function (row) { return row.count; }), backgroundColor: palette }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });
        }
    };

    ensureChartJs().then(render).catch(function () {});
})();
</script>
@endpush

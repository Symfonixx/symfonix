@section('title', __('user::fingerprint.title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => __('Team Management')],
            ['label' => __('user::fingerprint.title')],
        ];
    @endphp
    <x-admin.breadcrumb
        :pageTitle="__('user::fingerprint.title')"
        :breadcrumbItems="$breadcrumbItems"
        :pageDescription="__('user::fingerprint.page_description')"
    />
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <button type="button" class="btn btn-sm btn-light-primary" id="btn-test-connection" @disabled(! $stats['is_configured'])>
            <i class="bi bi-plug me-1"></i>{{ __('user::fingerprint.actions.test_connection') }}
        </button>
        <button type="button" class="btn btn-sm btn-light-info" id="btn-enroll-all" @disabled(! $stats['is_configured'])>
            <i class="bi bi-person-plus me-1"></i>{{ __('user::fingerprint.actions.enroll_all') }}
        </button>
        <button type="button" class="btn btn-sm btn-primary" id="btn-sync-attendance" @disabled(! $stats['is_configured'])>
            <i class="bi bi-arrow-repeat me-1"></i>{{ __('user::fingerprint.actions.sync_attendance') }}
        </button>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            const routes = {
                test: @json(route('admin.fingerprint.test-connection')),
                enrollAll: @json(route('admin.fingerprint.enroll-all')),
                sync: @json(route('admin.fingerprint.sync-attendance')),
                enrollEmployee: @json(url('/admin/fingerprint/employees')),
            };

            function runAction(url, method, $btn, successMessage) {
                const original = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>{{ __('user::fingerprint.processing') }}');

                makeAjaxRequest(url, method, null, 'json', function (res) {
                    $btn.prop('disabled', false).html(original);

                    if (res.success) {
                        toastr.success(res.message || successMessage);
                        if (url === routes.sync || url === routes.enrollAll) {
                            setTimeout(() => window.location.reload(), 800);
                        }
                    } else {
                        toastr.error(res.message || '{{ __('user::fingerprint.operation_failed') }}');
                    }
                }, function () {
                    $btn.prop('disabled', false).html(original);
                    toastr.error('{{ __('user::fingerprint.connection_failed') }}');
                });
            }

            $('#btn-test-connection').on('click', function () {
                runAction(routes.test, 'POST', $(this), '{{ __('user::fingerprint.messages.connection_ok') }}');
            });

            $('#btn-enroll-all').on('click', function () {
                Swal.fire({
                    text: '{{ __('user::fingerprint.confirm.enroll_all') }}',
                    icon: 'question',
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: '{{ __('user::fingerprint.confirm_yes') }}',
                    cancelButtonText: '{{ __('user::fingerprint.confirm_no') }}',
                    customClass: {
                        confirmButton: 'btn fw-bold btn-primary',
                        cancelButton: 'btn fw-bold btn-active-light-primary',
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        runAction(routes.enrollAll, 'POST', $('#btn-enroll-all'), '{{ __('user::fingerprint.messages.enroll_all_done') }}');
                    }
                });
            });

            $('#btn-sync-attendance').on('click', function () {
                runAction(routes.sync, 'POST', $(this), '{{ __('user::fingerprint.messages.sync_done') }}');
            });

            $('.enroll-employee').on('click', function () {
                const employeeId = $(this).data('id');
                const $btn = $(this);
                runAction(routes.enrollEmployee + '/' + employeeId + '/enroll', 'POST', $btn, '{{ __('user::fingerprint.messages.enroll_done') }}');
            });
        });
    </script>
@endsection

<x-admin-layout>
    @if(! $stats['is_configured'])
        <div class="alert alert-warning d-flex align-items-center mb-8">
            <i class="bi bi-exclamation-triangle fs-2 me-3"></i>
            <div>
                <div class="fw-bold">{{ __('user::fingerprint.not_configured.title') }}</div>
                <div class="text-muted">{{ __('user::fingerprint.not_configured.description') }}</div>
                <a href="{{ route('admin.system-configurations.index') }}" class="btn btn-sm btn-light-primary mt-3">
                    {{ __('user::fingerprint.not_configured.link') }}
                </a>
            </div>
        </div>
    @endif

    <div class="row g-5 mb-8">
        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body">
                    <div class="text-muted fs-7">{{ __('user::fingerprint.stats.enrolled') }}</div>
                    <div class="fs-2 fw-bold">{{ $stats['enrolled_employees'] }} / {{ $stats['active_employees'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body">
                    <div class="text-muted fs-7">{{ __('user::fingerprint.stats.logs') }}</div>
                    <div class="fs-2 fw-bold">{{ number_format($stats['total_logs']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-flush h-100">
                <div class="card-body">
                    <div class="text-muted fs-7">{{ __('user::fingerprint.stats.last_sync') }}</div>
                    <div class="fs-4 fw-bold">
                        {{ $stats['last_sync_at'] ? \Illuminate\Support\Carbon::parse($stats['last_sync_at'])->diffForHumans() : __('user::fingerprint.stats.never') }}
                    </div>
                    <div class="text-muted fs-8 mt-2">{{ __('user::fingerprint.stats.mapping_hint') }}</div>
                </div>
            </div>
        </div>
    </div>

    <x-admin.table :model="$logs" search="{{ __('user::fingerprint.search_logs') }}">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="min-w-150px">{{ __('Employees') }}</th>
            <th class="min-w-100px">{{ __('user::fingerprint.columns.device_user_id') }}</th>
            <th class="min-w-125px">{{ __('user::fingerprint.columns.punch') }}</th>
            <th class="min-w-125px">{{ __('user::fingerprint.columns.verify') }}</th>
            <th class="min-w-150px">{{ __('user::fingerprint.columns.recorded_at') }}</th>
            <th class="min-w-125px">{{ __('user::fingerprint.columns.synced_at') }}</th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @forelse($logs as $log)
            <tr>
                <td>
                    @if($log->employee)
                        <div class="fw-bold text-gray-800">{{ $log->employee->name }}</div>
                        <div class="text-muted fs-8">#{{ $log->employee_id }}</div>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td><code>{{ $log->device_user_id }}</code></td>
                <td>
                    <span class="badge badge-light-primary">{{ str_replace('_', ' ', $log->punchStateLabel()) }}</span>
                </td>
                <td>{{ $log->verifyModeLabel() ?? '—' }}</td>
                <td>{{ $log->recorded_at?->format('Y-m-d H:i:s') }}</td>
                <td>{{ $log->synced_at?->diffForHumans() ?? '—' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-10">{{ __('user::fingerprint.empty_logs') }}</td>
            </tr>
        @endforelse
        </tbody>
    </x-admin.table>
</x-admin-layout>

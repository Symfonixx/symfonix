@section('title', __('base::backup.title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => __('base::backup.title')],
        ];
    @endphp
    <x-admin.breadcrumb
        :pageTitle="__('base::backup.title')"
        :breadcrumbItems="$breadcrumbItems"
        :pageDescription="__('base::backup.page_description')"
    />
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <form method="POST" action="{{ route('admin.backups.store') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-1"></i>{{ __('base::backup.create_manual') }}
            </button>
        </form>
    </div>
@endsection

<x-admin-layout>
    <div class="row g-5 g-xl-8 mb-8">
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="symbol symbol-45px me-4">
                            <span class="symbol-label bg-light-primary">
                                <i class="bi bi-clock-history text-primary fs-2"></i>
                            </span>
                        </div>
                        <div>
                            <div class="fs-6 text-muted">{{ __('base::backup.auto_status') }}</div>
                            <div class="fs-4 fw-bold">
                                @if($settings['enabled'])
                                    <span class="text-success">{{ __('base::backup.enabled') }}</span>
                                @else
                                    <span class="text-muted">{{ __('base::backup.disabled') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-muted fs-7">
                        {{ __('base::backup.auto_hint') }}
                        <a href="{{ route('admin.system-configurations.index') }}" class="fw-semibold">
                            {{ __('base::system.title') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="fs-6 text-muted mb-2">{{ __('base::backup.interval_days') }}</div>
                    <div class="fs-2hx fw-bold">
                        {{ $settings['interval_days'] }}
                        <span class="fs-6 fw-semibold text-muted">{{ __('base::backup.days') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="fs-6 text-muted mb-2">{{ __('base::backup.last_auto_run') }}</div>
                    <div class="fs-4 fw-bold">
                        {{ $settings['last_run'] ? \Illuminate\Support\Carbon::parse($settings['last_run'])->format('Y-m-d H:i') : __('base::backup.never') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-8">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h2 class="fw-bold mb-0">{{ __('base::backup.import_title') }}</h2>
            </div>
        </div>
        <div class="card-body pt-0">
            <p class="text-muted fs-7 mb-5">{{ __('base::backup.import_hint') }}</p>
            <form method="POST"
                  action="{{ route('admin.backups.import') }}"
                  enctype="multipart/form-data"
                  onsubmit="return confirm(@js(__('base::backup.confirm_import')))">
                @csrf
                <div class="d-flex flex-column flex-md-row align-items-md-end gap-4">
                    <div class="flex-grow-1">
                        <label class="form-label" for="backup_file">{{ __('base::backup.backup_file') }}</label>
                        <input type="file"
                               id="backup_file"
                               name="backup_file"
                               class="form-control @error('backup_file') is-invalid @enderror"
                               accept=".zip,application/zip"
                               required>
                        @error('backup_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-upload me-1"></i>{{ __('base::backup.import_restore') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h2 class="fw-bold mb-0">{{ __('base::backup.list_title') }}</h2>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                    <tr class="text-start text-muted fw-bold fs-7 gs-0">
                        <th class="min-w-200px">{{ __('base::backup.filename') }}</th>
                        <th class="min-w-100px">{{ __('base::backup.type') }}</th>
                        <th class="min-w-100px">{{ __('base::backup.size') }}</th>
                        <th class="min-w-150px">{{ __('base::backup.created_at') }}</th>
                        <th class="text-end min-w-200px">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    @forelse($backups as $backup)
                        <tr>
                            <td>{{ $backup['filename'] }}</td>
                            <td>
                                @if($backup['type'] === 'auto')
                                    <span class="badge badge-light-info">{{ __('base::backup.type_auto') }}</span>
                                @elseif($backup['type'] === 'import')
                                    <span class="badge badge-light-warning">{{ __('base::backup.type_import') }}</span>
                                @else
                                    <span class="badge badge-light-primary">{{ __('base::backup.type_manual') }}</span>
                                @endif
                            </td>
                            <td>{{ number_format($backup['size'] / 1024, 1) }} KB</td>
                            <td>{{ \Illuminate\Support\Carbon::createFromTimestamp($backup['created_at'])->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                <form method="POST"
                                      action="{{ route('admin.backups.restore', $backup['filename']) }}"
                                      class="d-inline"
                                      onsubmit="return confirm(@js(__('base::backup.confirm_restore')))">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light-warning me-2" title="{{ __('base::backup.restore') }}">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </form>
                                <a href="{{ route('admin.backups.download', $backup['filename']) }}"
                                   class="btn btn-sm btn-light-primary me-2"
                                   title="{{ __('Download') }}">
                                    <i class="bi bi-download"></i>
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.backups.destroy', $backup['filename']) }}"
                                      class="d-inline"
                                      onsubmit="return confirm(@js(__('base::backup.confirm_delete')))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-10">
                                {{ __('base::backup.empty') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>

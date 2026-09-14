@props(['department' => 'finance', 'filters' => []])

<div class="d-flex align-items-center gap-2">
    <a class="btn btn-sm fw-bold btn-light-primary"
       href="{{ route('admin.reporting.'.$department.'.export', array_merge($filters, ['format' => 'csv'])) }}">
        <i class="bi bi-download me-1"></i>{{ __('reporting::report.actions.export_csv') }}
    </a>
    <a class="btn btn-sm fw-bold btn-light-danger"
       href="{{ route('admin.reporting.'.$department.'.export', array_merge($filters, ['format' => 'pdf'])) }}">
        <i class="bi bi-file-earmark-pdf me-1"></i>{{ __('reporting::report.actions.export_pdf') }}
    </a>
</div>

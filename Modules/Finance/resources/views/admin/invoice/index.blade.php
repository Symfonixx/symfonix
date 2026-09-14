@section('title', __('finance::invoice.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('finance::finance.menu.finance')],
            ['label' => __('finance::invoice.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('finance::invoice.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <x-can perform="finance.invoices.create">
        <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.finance.invoices.create') }}">
            {{ __('finance::invoice.actions.create') }} <i class="bi bi-plus-lg ms-1"></i>
        </a>
    </x-can>
@endsection

<x-admin-layout>
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" class="row g-4 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ __('finance::invoice.fields.status') }}</label>
                    <select name="status" class="form-select form-select-solid">
                        <option value="">{{ __('All') }}</option>
                        @foreach(['draft','sent','paid','overdue','void'] as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
                                {{ __('finance::invoice.status.'.$status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('finance::invoice.fields.company') }}</label>
                    <select name="company_id" class="form-select form-select-solid" data-control="select2">
                        <option value="">{{ __('All') }}</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" @selected((int)($filters['company_id'] ?? 0) === $company->id)>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed align-middle gy-4">
                    <thead>
                    <tr class="text-muted fw-bold fs-7">
                        <th>{{ __('finance::invoice.fields.invoice_number') }}</th>
                        <th>{{ __('finance::invoice.fields.company') }}</th>
                        <th>{{ __('finance::invoice.fields.total') }}</th>
                        <th>{{ __('finance::invoice.fields.status') }}</th>
                        <th>{{ __('finance::invoice.fields.due_at') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>
                                <a href="{{ route('admin.finance.invoices.show', $invoice) }}" class="text-hover-primary fw-bold">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $invoice->company?->name ?? __('N/A') }}</td>
                            <td>{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td>
                            <td>
                                @php
                                    $color = match($invoice->status) {
                                        'paid' => 'success',
                                        'overdue' => 'danger',
                                        'sent' => 'primary',
                                        'void' => 'secondary',
                                        default => 'warning',
                                    };
                                @endphp
                                <span class="badge badge-light-{{ $color }}">
                                    {{ __('finance::invoice.status.'.$invoice->status) }}
                                </span>
                            </td>
                            <td>{{ $invoice->due_at?->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.finance.invoices.pdf', $invoice) }}" class="btn btn-sm btn-light">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                                <form class="d-inline" method="POST" action="{{ route('admin.finance.invoices.destroy', $invoice) }}"
                                      data-confirm="{{ __('finance::invoice.messages.confirm_delete') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm">
                                        <i class="bi bi-trash fs-5"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted text-center py-10">{{ __('No records found') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $invoices->withQueryString()->links() }}
        </div>
    </div>
</x-admin-layout>

@section('title', __('crm::quote.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('CRM')],
            ['label' => __('crm::quote.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::quote.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <x-can perform="sales.quotes.create">
        <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.quotes.create') }}">
            <i class="bi bi-plus-lg me-1"></i>{{ __('crm::quote.actions.create') }}
        </a>
    </x-can>
@endsection

<x-admin-layout>
    <div class="card sx-filter-bar mb-6">
        <div class="card-body">
            <form method="GET" class="row g-4 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">{{ __('crm::quote.fields.status') }}</label>
                    <select name="status" class="form-select form-select-solid">
                        <option value="">{{ __('All') }}</option>
                        @foreach(['draft','sent','accepted','rejected','expired','void'] as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
                                {{ __('crm::quote.status.'.$status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('crm::quote.fields.company') }}</label>
                    <select name="company_id" class="form-select form-select-solid" data-control="select2">
                        <option value="">{{ __('All') }}</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" @selected((int)($filters['company_id'] ?? 0) === $company->id)>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Search') }}</label>
                    <input type="text" name="search" class="form-control form-control-solid"
                           value="{{ $filters['search'] ?? '' }}"
                           placeholder="{{ __('crm::quote.fields.quote_number') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i>{{ __('Filter') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-hover align-middle gy-4">
                    <thead>
                    <tr class="text-muted fw-bold fs-7">
                        <th>{{ __('crm::quote.fields.quote_number') }}</th>
                        <th>{{ __('crm::quote.fields.company') }}</th>
                        <th>{{ __('crm::quote.fields.deal') }}</th>
                        <th>{{ __('crm::quote.fields.total') }}</th>
                        <th>{{ __('crm::quote.fields.status') }}</th>
                        <th>{{ __('crm::quote.fields.expires_at') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    @forelse($quotes as $quote)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="sx-table-avatar bg-light-info text-info me-3">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </span>
                                    <a href="{{ route('admin.quotes.show', $quote) }}" class="text-hover-primary fw-bold">
                                        {{ $quote->quote_number }}
                                    </a>
                                </div>
                            </td>
                            <td>{{ $quote->company?->name ?? __('N/A') }}</td>
                            <td>{{ $quote->deal?->title ?? __('N/A') }}</td>
                            <td>{{ number_format($quote->total, 2) }} {{ $quote->currency }}</td>
                            <td>
                                @php
                                    $color = match($quote->status) {
                                        'accepted' => 'success',
                                        'rejected', 'void', 'expired' => 'danger',
                                        'sent' => 'primary',
                                        default => 'warning',
                                    };
                                @endphp
                                <span class="badge badge-light-{{ $color }}">
                                    {{ __('crm::quote.status.'.$quote->status) }}
                                </span>
                            </td>
                            <td>{{ $quote->expires_at?->format('Y-m-d') ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.quotes.show', $quote) }}" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1">
                                    <i class="bi bi-eye fs-5"></i>
                                </a>
                                <a href="{{ route('admin.quotes.pdf', $quote) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                    <i class="bi bi-file-pdf fs-5"></i>
                                </a>
                                @if(in_array($quote->status, ['draft', 'sent'], true))
                                    <a href="{{ route('admin.quotes.edit', $quote) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                        <i class="bi bi-pencil fs-5"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted text-center py-10">{{ __('No records found') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $quotes->withQueryString()->links() }}
        </div>
    </div>
</x-admin-layout>

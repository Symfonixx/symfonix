@section('title', __('tax::ledger.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('tax::tax.menu.tax')],
            ['label' => __('tax::ledger.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('tax::ledger.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <div class="card mb-5">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">{{ __('tax::ledger.filters.from') }}</label>
                    <input type="date" name="from" class="form-control form-control-solid" value="{{ request('from') }}"/>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('tax::ledger.filters.to') }}</label>
                    <input type="date" name="to" class="form-control form-control-solid" value="{{ request('to') }}"/>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('tax::ledger.filters.direction') }}</label>
                    <select name="direction" class="form-select form-select-solid">
                        <option value="">{{ __('tax::ledger.filters.all') }}</option>
                        <option value="output" @selected(request('direction') === 'output')>{{ __('tax::ledger.directions.output') }}</option>
                        <option value="input" @selected(request('direction') === 'input')>{{ __('tax::ledger.directions.input') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('tax::ledger.pages.index_title') }}</h3>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                    <tr class="text-muted fw-bold fs-7">
                        <th>{{ __('tax::ledger.fields.date') }}</th>
                        <th>{{ __('tax::ledger.fields.direction') }}</th>
                        <th>{{ __('tax::ledger.fields.tax_rate') }}</th>
                        <th>{{ __('tax::ledger.fields.amount') }}</th>
                        <th>{{ __('tax::ledger.fields.company') }}</th>
                        <th>{{ __('tax::ledger.fields.project') }}</th>
                        <th>{{ __('tax::ledger.fields.description') }}</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    @forelse($entries as $entry)
                        <tr>
                            <td>{{ $entry->transaction_date->format('Y-m-d') }}</td>
                            <td>
                                <span class="badge badge-light-{{ $entry->direction === 'output' ? 'success' : 'warning' }}">
                                    {{ __('tax::ledger.directions.'.$entry->direction) }}
                                </span>
                            </td>
                            <td>{{ $entry->taxRate?->name ?? '—' }}</td>
                            <td>{{ number_format((float) $entry->amount, 2) }} {{ $entry->currency }}</td>
                            <td>{{ $entry->company?->name ?? '—' }}</td>
                            <td>{{ $entry->project?->title ?? '—' }}</td>
                            <td>{{ $entry->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-10">{{ __('No records found') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $entries->links() }}
        </div>
    </div>
</x-admin-layout>

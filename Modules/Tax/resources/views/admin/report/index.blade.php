@section('title', __('tax::report.pages.filing_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('tax::tax.menu.tax')],
            ['label' => __('tax::report.pages.filing_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('tax::report.pages.filing_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2">
        <a class="btn btn-sm fw-bold btn-light-primary"
           href="{{ route('admin.tax.reports.filing.export', $filters) }}">
            <i class="bi bi-download me-1"></i>{{ __('tax::report.actions.export_csv') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <div class="card mb-5">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">{{ __('tax::report.fields.from') }}</label>
                    <input type="date" name="from" class="form-control form-control-solid" value="{{ $filters['from'] }}" required/>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('tax::report.fields.to') }}</label>
                    <input type="date" name="to" class="form-control form-control-solid" value="{{ $filters['to'] }}" required/>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('tax::report.fields.tax_rate') }}</label>
                    <select name="tax_rate_id" class="form-select form-select-solid">
                        <option value="">{{ __('tax::report.fields.all_rates') }}</option>
                        @foreach($taxRates as $rate)
                            <option value="{{ $rate->id }}" @selected((int) ($filters['tax_rate_id'] ?? 0) === $rate->id)>
                                {{ $rate->name }} ({{ number_format((float) $rate->percentage, 2) }}%)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('tax::report.fields.company') }}</label>
                    <select name="company_id" class="form-select form-select-solid">
                        <option value="">{{ __('tax::report.fields.all_companies') }}</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" @selected((int) ($filters['company_id'] ?? 0) === $company->id)>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">{{ __('tax::report.actions.generate') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-5 mb-5">
        <div class="col-md-4">
            <div class="card card-flush h-100">
                <div class="card-body">
                    <span class="text-muted fs-7">{{ __('tax::report.fields.output_tax') }}</span>
                    <div class="fs-2 fw-bold text-success">{{ number_format($report['output_tax'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-flush h-100">
                <div class="card-body">
                    <span class="text-muted fs-7">{{ __('tax::report.fields.input_tax') }}</span>
                    <div class="fs-2 fw-bold text-warning">{{ number_format($report['input_tax'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-flush h-100 border-primary">
                <div class="card-body">
                    <span class="text-muted fs-7">{{ __('tax::report.fields.net_tax_payable') }}</span>
                    <div class="fs-2 fw-bold text-primary">{{ number_format($report['net_tax_payable'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header"><h3 class="card-title">{{ __('tax::report.sections.by_rate') }}</h3></div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed align-middle gs-0 gy-4">
                    <thead>
                    <tr class="text-muted fw-bold fs-7">
                        <th>{{ __('tax::report.fields.tax_rate') }}</th>
                        <th>{{ __('tax::report.fields.output_tax') }}</th>
                        <th>{{ __('tax::report.fields.input_tax') }}</th>
                        <th>{{ __('tax::report.fields.net_tax_payable') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($report['by_rate'] as $row)
                        <tr>
                            <td>{{ $row['tax_rate_name'] }} @if($row['percentage']) ({{ number_format((float) $row['percentage'], 2) }}%) @endif</td>
                            <td>{{ number_format($row['output_tax'], 2) }}</td>
                            <td>{{ number_format($row['input_tax'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($row['net_tax'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-6">{{ __('No records found') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>

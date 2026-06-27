@section('title', __('finance::invoice.pages.create_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('finance::invoice.pages.index_title'), 'url' => route('admin.finance.invoices.index')],
            ['label' => __('finance::invoice.pages.create_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('finance::invoice.pages.create_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.finance.invoices.store') }}" id="invoice-form">
                @csrf
                <div class="row g-5 mb-8">
                    <div class="col-md-6">
                        <label class="form-label required">{{ __('finance::invoice.fields.company') }}</label>
                        <select name="company_id" class="form-select form-select-solid @error('company_id') is-invalid @enderror" required data-control="select2">
                            <option value="">{{ __('finance::invoice.fields.select_company') }}</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label required">{{ __('finance::invoice.fields.currency') }}</label>
                        <input type="text" name="currency" maxlength="3" class="form-control form-control-solid text-uppercase"
                               value="{{ old('currency', config('finance.default_currency', 'USD')) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('finance::invoice.fields.tax') }}</label>
                        <input type="number" step="0.01" min="0" name="tax_amount" class="form-control form-control-solid" value="{{ old('tax_amount', 0) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required">{{ __('finance::invoice.fields.issued_at') }}</label>
                        <input type="date" name="issued_at" class="form-control form-control-solid" value="{{ old('issued_at', now()->toDateString()) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('finance::invoice.fields.due_at') }}</label>
                        <input type="date" name="due_at" class="form-control form-control-solid" value="{{ old('due_at') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('finance::invoice.fields.notes') }}</label>
                        <textarea name="notes" rows="2" class="form-control form-control-solid">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <h4 class="fw-bold mb-4">{{ __('finance::invoice.fields.line_items') }}</h4>
                <div id="line-items">
                    @php $oldLines = old('lines', [['description' => '', 'quantity' => 1, 'unit_price' => '']]); @endphp
                    @foreach($oldLines as $index => $line)
                        <div class="row g-3 mb-3 line-row">
                            <div class="col-md-5">
                                <input type="text" name="lines[{{ $index }}][description]" class="form-control form-control-solid"
                                       placeholder="{{ __('finance::invoice.fields.description') }}"
                                       value="{{ $line['description'] ?? '' }}" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" min="1" name="lines[{{ $index }}][quantity]" class="form-control form-control-solid"
                                       value="{{ $line['quantity'] ?? 1 }}" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" step="0.01" min="0" name="lines[{{ $index }}][unit_price]" class="form-control form-control-solid"
                                       placeholder="0.00" value="{{ $line['unit_price'] ?? '' }}" required>
                            </div>
                            <div class="col-md-2">
                                @if($index > 0)
                                    <button type="button" class="btn btn-light-danger w-100 remove-line">&times;</button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-light-primary btn-sm mb-8" id="add-line">
                    <i class="bi bi-plus-lg me-1"></i>{{ __('finance::invoice.actions.add_line') }}
                </button>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    <a href="{{ route('admin.finance.invoices.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let lineIndex = document.querySelectorAll('.line-row').length;
                const container = document.getElementById('line-items');

                document.getElementById('add-line')?.addEventListener('click', function () {
                    const row = document.createElement('div');
                    row.className = 'row g-3 mb-3 line-row';
                    row.innerHTML = `
                        <div class="col-md-5">
                            <input type="text" name="lines[${lineIndex}][description]" class="form-control form-control-solid" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" min="1" name="lines[${lineIndex}][quantity]" class="form-control form-control-solid" value="1" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" step="0.01" min="0" name="lines[${lineIndex}][unit_price]" class="form-control form-control-solid" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-light-danger w-100 remove-line">&times;</button>
                        </div>`;
                    container.appendChild(row);
                    lineIndex++;
                });

                container.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-line')) {
                        e.target.closest('.line-row')?.remove();
                    }
                });
            });
        </script>
    @endpush
</x-admin-layout>

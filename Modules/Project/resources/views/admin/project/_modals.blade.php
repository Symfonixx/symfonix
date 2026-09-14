@can('update', $project)
    {{-- Assign employee --}}
    <div class="modal fade" id="assignEmployeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.projects.employees.store', $project) }}">
                    @csrf
                    <div class="modal-header">
                        <h2 class="fw-bold">{{ __('project::project.actions.assign_employee') }}</h2>
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                        <div class="mb-5">
                            <label class="form-label required" for="employee_id">{{ __('project::project.fields.employee') }}</label>
                            <select id="employee_id" name="employee_id"
                                    class="form-select form-select-solid @error('employee_id') is-invalid @enderror" required>
                                <option value="">{{ __('project::project.fields.select_employee') }}</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected((int) old('employee_id') === $employee->id)>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="row g-5 mb-5">
                            <div class="col-md-6">
                                <label class="form-label" for="role">{{ __('project::project.fields.role') }}</label>
                                <input type="text" id="role" name="role" value="{{ old('role') }}"
                                       class="form-control form-control-solid"
                                       placeholder="{{ __('project::project.placeholders.role') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required" for="started_at">{{ __('project::project.fields.started_at') }}</label>
                                <input type="date" id="started_at" name="started_at"
                                       class="form-control form-control-solid @error('started_at') is-invalid @enderror"
                                       value="{{ old('started_at', now()->toDateString()) }}" required>
                                @error('started_at') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label" for="notes">{{ __('project::project.fields.notes') }}</label>
                            <input type="text" id="notes" name="notes" value="{{ old('notes') }}"
                                   class="form-control form-control-solid">
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus me-1"></i>{{ __('project::project.actions.assign_employee') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Finish assignment modals --}}
    @foreach($profitAndLoss['assignments'] as $row)
        @if($row['is_active'])
            @php $assignment = $row['assignment']; @endphp
            <div class="modal fade" id="finishAssignmentModal{{ $assignment->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mw-450px">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.projects.employees.finish', [$project, $assignment]) }}">
                            @csrf
                            <div class="modal-header">
                                <h2 class="fw-bold">{{ __('project::project.actions.finish_work') }}</h2>
                                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                                </div>
                            </div>
                            <div class="modal-body scroll-y mx-5 my-7">
                                <p class="text-muted mb-5">
                                    {{ $assignment->employee?->name }}
                                </p>
                                <label class="form-label required" for="ended_at_{{ $assignment->id }}">
                                    {{ __('project::project.fields.ended_at') }}
                                </label>
                                <input type="date" id="ended_at_{{ $assignment->id }}" name="ended_at"
                                       class="form-control form-control-solid"
                                       value="{{ now()->toDateString() }}"
                                       min="{{ $assignment->started_at?->format('Y-m-d') }}" required>
                            </div>
                            <div class="modal-footer flex-center">
                                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                <button type="submit" class="btn btn-warning">
                                    {{ __('project::project.actions.finish_work') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endcan

@can('project.projects.edit')
    {{-- Log expense --}}
    <div class="modal fade" id="logExpenseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.projects.expenses.store', $project) }}">
                    @csrf
                    <div class="modal-header">
                        <h2 class="fw-bold">{{ __('project::project.actions.log_expense') }}</h2>
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                        <div class="row g-5 mb-5">
                            <div class="col-md-6">
                                <label class="form-label required" for="expense_amount">{{ __('finance::finance.fields.amount') }}</label>
                                <input type="number" step="0.01" min="0.01" id="expense_amount" name="amount"
                                       class="form-control form-control-solid @error('amount') is-invalid @enderror"
                                       value="{{ old('amount') }}" required>
                                @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required" for="expense_currency">{{ __('finance::finance.fields.currency') }}</label>
                                <input type="text" id="expense_currency" name="currency" maxlength="3"
                                       class="form-control form-control-solid text-uppercase @error('currency') is-invalid @enderror"
                                       value="{{ old('currency', $profitAndLoss['currency']) }}" required>
                                @error('currency') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="mb-5">
                            <label class="form-label required" for="expense_category_id">{{ __('finance::finance.fields.category') }}</label>
                            <select id="expense_category_id" name="expense_category_id"
                                    class="form-select form-select-solid @error('expense_category_id') is-invalid @enderror" required>
                                <option value="">{{ __('Select') }}...</option>
                                @foreach($expenseCategories as $category)
                                    <option value="{{ $category->id }}" @selected((int) old('expense_category_id') === $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('expense_category_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-5">
                            <label class="form-label" for="expense_tax_rate_id">{{ __('tax::tax_rate.fields.name') }}</label>
                            <x-tax::tax-rate-select
                                name="tax_rate_id"
                                id="expense_tax_rate_id"
                                :selected="old('tax_rate_id', $project->tax_rate_id)"
                                :tax-rates="$taxRates ?? []"
                            />
                            @error('tax_rate_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="row g-5">
                            <div class="col-md-6">
                                <label class="form-label required" for="expense_date">{{ __('finance::finance.fields.date') }}</label>
                                <input type="date" id="expense_date" name="transaction_date"
                                       class="form-control form-control-solid @error('transaction_date') is-invalid @enderror"
                                       value="{{ old('transaction_date', now()->toDateString()) }}" required>
                                @error('transaction_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="expense_description">{{ __('finance::finance.fields.description') }}</label>
                                <input type="text" id="expense_description" name="description" value="{{ old('description') }}"
                                       class="form-control form-control-solid">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-cash-stack me-1"></i>{{ __('project::project.actions.log_expense') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

@can('finance.invoices.create')
    @if(isset($collectionSummary) && $collectionSummary['remaining'] > 0)
        {{-- Add invoice --}}
        <div class="modal fade" id="addInvoiceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.projects.invoices.store', $project) }}" id="project-invoice-form">
                        @csrf
                        <div class="modal-header">
                            <h2 class="fw-bold">{{ __('project::project.sections.add_invoice') }}</h2>
                            <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                            </div>
                        </div>
                        <div class="modal-body scroll-y mx-5 mx-xl-10 my-7">
                            <div class="alert alert-primary d-flex align-items-center p-4 mb-6">
                                <i class="bi bi-info-circle fs-2 text-primary me-3"></i>
                                <div class="fs-7">
                                    {{ __('project::project.fields.remaining') }}:
                                    <strong>{{ number_format($collectionSummary['remaining'], 2) }} {{ $collectionSummary['currency'] }}</strong>
                                </div>
                            </div>

                            <div class="row g-5 mb-6">
                                <div class="col-md-3">
                                    <label class="form-label required">{{ __('finance::invoice.fields.currency') }}</label>
                                    <input type="text" name="currency" maxlength="3"
                                           class="form-control form-control-solid text-uppercase @error('currency') is-invalid @enderror"
                                           value="{{ old('currency', $collectionSummary['currency']) }}" required>
                                    @error('currency')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required">{{ __('finance::invoice.fields.issued_at') }}</label>
                                    <input type="date" name="issued_at"
                                           class="form-control form-control-solid @error('issued_at') is-invalid @enderror"
                                           value="{{ old('issued_at', now()->toDateString()) }}" required>
                                    @error('issued_at')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('finance::invoice.fields.due_at') }}</label>
                                    <input type="date" name="due_at" class="form-control form-control-solid"
                                           value="{{ old('due_at') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('tax::tax_rate.fields.name') }}</label>
                                    <x-tax::tax-rate-select
                                        name="tax_rate_id"
                                        id="invoice_tax_rate_id"
                                        :selected="old('tax_rate_id', $project->tax_rate_id)"
                                        :tax-rates="$taxRates ?? []"
                                    />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('finance::invoice.fields.notes') }}</label>
                                    <textarea name="notes" rows="2" class="form-control form-control-solid">{{ old('notes') }}</textarea>
                                </div>
                            </div>

                            <h5 class="fw-bold mb-4">{{ __('finance::invoice.fields.line_items') }}</h5>
                            @error('lines')<div class="text-danger fs-7 mb-3">{{ $message }}</div>@enderror
                            <div id="project-line-items">
                                @php
                                    $oldLines = old('lines', [[
                                        'description' => $project->title,
                                        'quantity' => 1,
                                        'unit_price' => $collectionSummary['remaining'],
                                        'tax_rate_id' => $project->tax_rate_id,
                                    ]]);
                                    $projectTaxRates = $taxRates ?? [];
                                @endphp
                                @foreach($oldLines as $index => $line)
                                    <div class="row g-3 mb-3 line-row">
                                        <div class="col-md-4">
                                            <input type="text" name="lines[{{ $index }}][description]"
                                                   class="form-control form-control-solid"
                                                   placeholder="{{ __('finance::invoice.fields.description') }}"
                                                   value="{{ $line['description'] ?? '' }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" min="1" name="lines[{{ $index }}][quantity]"
                                                   class="form-control form-control-solid line-qty"
                                                   value="{{ $line['quantity'] ?? 1 }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" step="0.01" min="0" name="lines[{{ $index }}][unit_price]"
                                                   class="form-control form-control-solid line-price"
                                                   placeholder="0.00" value="{{ $line['unit_price'] ?? '' }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <select name="lines[{{ $index }}][tax_rate_id]" class="form-select form-select-solid line-tax-rate">
                                                <option value="">{{ __('tax::tax_rate.fields.none') }}</option>
                                                @foreach($projectTaxRates as $rate)
                                                    <option value="{{ $rate->id }}"
                                                            data-percentage="{{ $rate->percentage }}"
                                                            data-type="{{ $rate->type }}"
                                                            @selected((int) ($line['tax_rate_id'] ?? $project->tax_rate_id) === $rate->id)>
                                                        {{ $rate->name }} ({{ number_format((float) $rate->percentage, 2) }}%)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            @if($index > 0)
                                                <button type="button" class="btn btn-light-danger w-100 remove-line">&times;</button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-end text-muted fs-7 mb-3" id="project-invoice-totals"></div>
                            <button type="button" class="btn btn-light-primary btn-sm" id="project-add-line">
                                <i class="bi bi-plus-lg me-1"></i>{{ __('finance::invoice.actions.add_line') }}
                            </button>
                        </div>
                        <div class="modal-footer flex-center">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-receipt me-1"></i>{{ __('project::project.actions.add_invoice') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endcan

@push('scripts')
    @php
        $projectTaxRateOptions = collect($taxRates ?? [])->map(static fn ($rate) => [
            'id' => $rate->id,
            'name' => $rate->name,
            'percentage' => $rate->percentage,
            'type' => $rate->type,
        ])->values();
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let lineIndex = document.querySelectorAll('#project-line-items .line-row').length;
            const container = document.getElementById('project-line-items');
            const defaultTaxRateId = @json(old('tax_rate_id', $project->tax_rate_id));
            const taxRateOptions = @json($projectTaxRateOptions);

            function buildTaxOptions(selectedId) {
                let html = `<option value="">{{ __('tax::tax_rate.fields.none') }}</option>`;
                taxRateOptions.forEach(function (rate) {
                    const selected = String(selectedId || defaultTaxRateId) === String(rate.id) ? 'selected' : '';
                    html += `<option value="${rate.id}" data-percentage="${rate.percentage}" data-type="${rate.type}" ${selected}>${rate.name} (${parseFloat(rate.percentage).toFixed(2)}%)</option>`;
                });
                return html;
            }

            function calcLineTotal(qty, price, rateEl) {
                const base = qty * price;
                if (!rateEl || !rateEl.value) return base;
                const pct = parseFloat(rateEl.selectedOptions[0]?.dataset.percentage || 0);
                const type = rateEl.selectedOptions[0]?.dataset.type || 'exclusive';
                if (type === 'inclusive') return base;
                return base + (base * pct / 100);
            }

            function updateProjectInvoiceTotals() {
                const totalsEl = document.getElementById('project-invoice-totals');
                if (!totalsEl || !container) return;
                let subtotal = 0, tax = 0, total = 0;
                container.querySelectorAll('.line-row').forEach(function (row) {
                    const qty = parseFloat(row.querySelector('.line-qty')?.value || 0);
                    const price = parseFloat(row.querySelector('.line-price')?.value || 0);
                    const rateEl = row.querySelector('.line-tax-rate');
                    const base = qty * price;
                    subtotal += base;
                    if (rateEl?.value) {
                        const pct = parseFloat(rateEl.selectedOptions[0]?.dataset.percentage || 0);
                        const type = rateEl.selectedOptions[0]?.dataset.type || 'exclusive';
                        if (type === 'inclusive') {
                            const lineTax = base * (pct / (100 + pct));
                            tax += lineTax;
                            total += base;
                        } else {
                            const lineTax = base * (pct / 100);
                            tax += lineTax;
                            total += base + lineTax;
                        }
                    } else {
                        total += base;
                    }
                });
                totalsEl.textContent = `{{ __('finance::invoice.fields.subtotal') }}: ${subtotal.toFixed(2)} | {{ __('tax::report.fields.output_tax') }}: ${tax.toFixed(2)} | {{ __('finance::invoice.fields.total') }}: ${total.toFixed(2)}`;
            }

            document.getElementById('project-add-line')?.addEventListener('click', function () {
                const row = document.createElement('div');
                row.className = 'row g-3 mb-3 line-row';
                row.innerHTML = `
                    <div class="col-md-4">
                        <input type="text" name="lines[${lineIndex}][description]" class="form-control form-control-solid" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" min="1" name="lines[${lineIndex}][quantity]" class="form-control form-control-solid line-qty" value="1" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" min="0" name="lines[${lineIndex}][unit_price]" class="form-control form-control-solid line-price" required>
                    </div>
                    <div class="col-md-3">
                        <select name="lines[${lineIndex}][tax_rate_id]" class="form-select form-select-solid line-tax-rate">${buildTaxOptions(defaultTaxRateId)}</select>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-light-danger w-100 remove-line">&times;</button>
                    </div>`;
                container.appendChild(row);
                lineIndex++;
                updateProjectInvoiceTotals();
            });

            container?.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-line')) {
                    e.target.closest('.line-row')?.remove();
                    updateProjectInvoiceTotals();
                }
            });

            container?.addEventListener('input', updateProjectInvoiceTotals);
            container?.addEventListener('change', updateProjectInvoiceTotals);
            document.getElementById('invoice_tax_rate_id')?.addEventListener('change', function () {
                container.querySelectorAll('.line-tax-rate').forEach(function (el) {
                    if (!el.value) el.value = this.value;
                }.bind(this));
                updateProjectInvoiceTotals();
            });
            updateProjectInvoiceTotals();

            @if($errors->hasAny(['employee_id', 'started_at']))
                const assignModal = document.getElementById('assignEmployeeModal');
                if (assignModal) new bootstrap.Modal(assignModal).show();
            @elseif($errors->hasAny(['amount', 'expense_category_id', 'transaction_date']))
                const expenseModal = document.getElementById('logExpenseModal');
                if (expenseModal) new bootstrap.Modal(expenseModal).show();
            @elseif($errors->hasAny(['issued_at', 'lines', 'tax_amount', 'due_at']) || ($errors->has('currency') && old('issued_at')))
                const invoiceModal = document.getElementById('addInvoiceModal');
                if (invoiceModal) new bootstrap.Modal(invoiceModal).show();
            @endif
        });
    </script>
@endpush

@if(isset($collectionSummary))
    <div class="card mb-6">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('project::project.sections.collection') }}</h3>
        </div>
        <div class="card-body pt-0">
            <div class="row g-5">
                <div class="col-md-2">
                    <div class="text-muted fs-7">{{ __('project::project.fields.payment_status') }}</div>
                    @php
                        $paymentColor = match($collectionSummary['payment_status']) {
                            'fully_paid' => 'success',
                            'partially_paid' => 'warning',
                            default => 'danger',
                        };
                    @endphp
                    <span class="badge badge-light-{{ $paymentColor }} mt-1">
                        {{ __('project::project.payment_status.'.$collectionSummary['payment_status']) }}
                    </span>
                </div>
                <div class="col-md-2">
                    <div class="text-muted fs-7">{{ __('project::project.fields.budget') }}</div>
                    <div class="fw-bold fs-4">
                        {{ number_format($collectionSummary['budget'], 2) }} {{ $collectionSummary['currency'] }}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-muted fs-7">{{ __('project::project.fields.invoiced') }}</div>
                    <div class="fw-bold fs-4">
                        {{ number_format($collectionSummary['invoiced'], 2) }} {{ $collectionSummary['currency'] }}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-muted fs-7">{{ __('project::project.fields.remaining') }}</div>
                    <div class="fw-bold fs-4 {{ $collectionSummary['remaining'] <= 0 ? 'text-success' : 'text-primary' }}">
                        {{ number_format($collectionSummary['remaining'], 2) }} {{ $collectionSummary['currency'] }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted fs-7">{{ __('project::project.fields.collection_rate') }}</div>
                    <div class="fw-bold fs-4">{{ $collectionSummary['collection_rate'] }}%</div>
                    <div class="progress h-6px mt-2">
                        <div class="progress-bar bg-primary" style="width: {{ $collectionSummary['collection_rate'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@can('Finance Management')
    @if(isset($collectionSummary) && $collectionSummary['remaining'] > 0)
        <div class="card mb-6">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title fw-bold">{{ __('project::project.sections.add_invoice') }}</h3>
            </div>
            <div class="card-body pt-0">
                <form method="POST" action="{{ route('admin.projects.invoices.store', $project) }}" id="project-invoice-form">
                    @csrf
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
                            <label class="form-label">{{ __('finance::invoice.fields.tax') }}</label>
                            <input type="number" step="0.01" min="0" name="tax_amount"
                                   class="form-control form-control-solid" value="{{ old('tax_amount', 0) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('finance::invoice.fields.notes') }}</label>
                            <textarea name="notes" rows="2" class="form-control form-control-solid">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-4">{{ __('finance::invoice.fields.line_items') }}</h5>
                    @error('lines')<div class="text-danger fs-7 mb-3">{{ $message }}</div>@enderror
                    <div id="project-line-items">
                        @php $oldLines = old('lines', [['description' => $project->title, 'quantity' => 1, 'unit_price' => $collectionSummary['remaining']]]); @endphp
                        @foreach($oldLines as $index => $line)
                            <div class="row g-3 mb-3 line-row">
                                <div class="col-md-5">
                                    <input type="text" name="lines[{{ $index }}][description]"
                                           class="form-control form-control-solid"
                                           placeholder="{{ __('finance::invoice.fields.description') }}"
                                           value="{{ $line['description'] ?? '' }}" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" min="1" name="lines[{{ $index }}][quantity]"
                                           class="form-control form-control-solid"
                                           value="{{ $line['quantity'] ?? 1 }}" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" step="0.01" min="0" name="lines[{{ $index }}][unit_price]"
                                           class="form-control form-control-solid"
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
                    <button type="button" class="btn btn-light-primary btn-sm mb-6" id="project-add-line">
                        <i class="bi bi-plus-lg me-1"></i>{{ __('finance::invoice.actions.add_line') }}
                    </button>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-receipt me-1"></i>{{ __('project::project.actions.add_invoice') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let lineIndex = document.querySelectorAll('#project-line-items .line-row').length;
                const container = document.getElementById('project-line-items');

                document.getElementById('project-add-line')?.addEventListener('click', function () {
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

                container?.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-line')) {
                        e.target.closest('.line-row')?.remove();
                    }
                });
            });
        </script>
    @endpush
@endcan

<div class="card mb-6">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('project::project.sections.invoices') }}</h3>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed align-middle gs-0 gy-4">
                    <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th>{{ __('finance::invoice.fields.invoice_number') }}</th>
                        <th>{{ __('finance::invoice.fields.total') }}</th>
                        <th>{{ __('finance::invoice.fields.status') }}</th>
                        <th>{{ __('finance::invoice.fields.issued_at') }}</th>
                        <th>{{ __('finance::invoice.fields.due_at') }}</th>
                        <th class="text-end"></th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    @forelse($project->invoices as $invoice)
                        <tr>
                            <td>
                                <a href="{{ route('admin.finance.invoices.show', $invoice) }}" class="text-hover-primary fw-bold">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
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
                            <td>{{ $invoice->issued_at?->format('Y-m-d') }}</td>
                            <td>{{ $invoice->due_at?->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.finance.invoices.pdf', $invoice) }}" class="btn btn-sm btn-light">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted text-center py-10">
                                {{ __('project::project.messages.no_invoices') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<div class="mb-10">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-6">
        <div>
            <h4 class="fw-bold text-gray-900 mb-1">{{ __('project::project.sections.invoices') }}</h4>
            <p class="text-muted fs-7 mb-0">{{ __('project::project.sections.collection') }}</p>
        </div>
        @can('finance.invoices.create')
            @if(isset($collectionSummary) && $collectionSummary['remaining'] > 0)
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addInvoiceModal">
                    <i class="bi bi-receipt me-1"></i>{{ __('project::project.actions.add_invoice') }}
                </button>
            @endif
        @endcan
    </div>

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
                    <td>{{ $invoice->due_at?->format('Y-m-d') ?: '—' }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.finance.invoices.pdf', $invoice) }}"
                           class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm"
                           title="PDF">
                            <i class="bi bi-file-pdf"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-muted text-center py-10">
                        <div class="mb-3">
                            <i class="bi bi-receipt fs-2x text-gray-400"></i>
                        </div>
                        {{ __('project::project.messages.no_invoices') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-6">
        <div>
            <h4 class="fw-bold text-gray-900 mb-1">{{ __('project::project.sections.expenses') }}</h4>
        </div>
        @can('project.projects.edit')
            <button type="button" class="btn btn-sm btn-light-danger" data-bs-toggle="modal" data-bs-target="#logExpenseModal">
                <i class="bi bi-cash-stack me-1"></i>{{ __('project::project.actions.log_expense') }}
            </button>
        @endcan
    </div>

    <div class="table-responsive">
        <table class="table table-row-dashed align-middle gs-0 gy-4">
            <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>{{ __('finance::finance.fields.date') }}</th>
                <th>{{ __('finance::finance.fields.category') }}</th>
                <th>{{ __('finance::finance.fields.description') }}</th>
                <th class="text-end">{{ __('finance::finance.fields.amount') }}</th>
            </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">
            @forelse($profitAndLoss['expense_entries'] as $entry)
                @php
                    $category = $entry->lines
                        ->firstWhere('account', \Modules\Finance\Models\JournalLine::ACCOUNT_EXPENSE)
                        ?->expenseCategory;
                @endphp
                <tr>
                    <td>{{ $entry->transaction_date?->format('Y-m-d') }}</td>
                    <td>
                        <span class="badge badge-light">{{ $category?->name ?: '—' }}</span>
                    </td>
                    <td>{{ $entry->description ?: '—' }}</td>
                    <td class="text-end fw-bold">{{ number_format($entry->amount, 2) }} {{ $entry->currency }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-muted text-center py-10">
                        <div class="mb-3">
                            <i class="bi bi-cash-stack fs-2x text-gray-400"></i>
                        </div>
                        {{ __('project::project.messages.no_expenses') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

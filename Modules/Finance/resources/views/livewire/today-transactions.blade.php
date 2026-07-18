@php
    $sideLabel = fn (string $flow) => $flow === 'revenue'
        ? __('finance::finance.fields.credit')
        : __('finance::finance.fields.debit');
    $sideColor = fn (string $flow) => $flow === 'revenue' ? 'success' : 'danger';
@endphp

<div>
    <div class="card card-flush border-0 shadow-sm h-100">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('finance::finance.metrics.today_transactions') }}</h3>
            <div class="card-toolbar">
                <div class="d-flex flex-wrap align-items-end gap-3">
                    <div>
                        <label class="form-label fs-8 text-muted mb-1">{{ __('finance::finance.filters.from_date') }}</label>
                        <input type="date" wire:model.live="fromDate" class="form-control form-control-sm form-control-solid" max="{{ $toDate ?: null }}"/>
                    </div>
                    <div>
                        <label class="form-label fs-8 text-muted mb-1">{{ __('finance::finance.filters.to_date') }}</label>
                        <input type="date" wire:model.live="toDate" class="form-control form-control-sm form-control-solid" min="{{ $fromDate ?: null }}"/>
                    </div>
                    <button type="button" wire:click="resetFilters" class="btn btn-sm btn-light-primary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('finance::finance.filters.reset') }}
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body pt-0">
            @if (session('finance_log_success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('finance_log_success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('finance_log_error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('finance_log_error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($entries->isEmpty())
                <p class="text-muted mb-0">{{ __('finance::finance.filters.no_transactions_in_range') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3">
                        <thead>
                        <tr class="text-muted fw-semibold fs-7">
                            <th>{{ __('finance::finance.fields.entry_type') }}</th>
                            <th>{{ __('finance::finance.fields.amount') }}</th>
                            <th>{{ __('finance::finance.fields.category') }}</th>
                            <th>{{ __('finance::finance.fields.description') }}</th>
                            <th>{{ __('finance::finance.fields.date') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($entries as $entry)
                            @php
                                $expenseLine = $entry->lines->firstWhere('account', 'expense');
                            @endphp
                            <tr wire:key="journal-entry-{{ $entry->id }}">
                                <td>
                                    <span class="badge badge-light-{{ $sideColor($entry->flow) }}">
                                        {{ $sideLabel($entry->flow) }}
                                    </span>
                                </td>
                                <td class="fw-bold">
                                    <x-finance-money
                                        :amount="$entry->amount"
                                        :currency="$entry->currency"
                                        :exchange-rate="$entry->exchange_rate"
                                        :base-amount="$entry->base_amount"
                                    />
                                    <div class="text-muted fs-8">{{ number_format($entry->amount, 2) }} {{ $entry->currency }}</div>
                                </td>
                                <td>{{ $expenseLine?->expenseCategory?->name ?? '—' }}</td>
                                <td class="text-muted">{{ \Illuminate\Support\Str::limit($entry->description, 50) ?: '—' }}</td>
                                <td class="text-muted">{{ $entry->transaction_date?->format('Y-m-d') }}</td>
                                <td class="text-end">
                                    @if($this->canDelete($entry))
                                        <button type="button"
                                                class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm"
                                                x-data
                                                @click.prevent="
                                                    Swal.fire({
                                                        text: @js(__('finance::finance.messages.confirm_delete_transaction')),
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        buttonsStyling: false,
                                                        confirmButtonText: @js(__('Yes Delete!')),
                                                        cancelButtonText: @js(__('No Cancel')),
                                                        customClass: {
                                                            confirmButton: 'btn fw-bold btn-danger',
                                                            cancelButton: 'btn fw-bold btn-active-light-primary'
                                                        }
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            $wire.deleteTransaction({{ $entry->id }});
                                                        }
                                                    })
                                                "
                                                wire:loading.attr="disabled"
                                                wire:target="deleteTransaction({{ $entry->id }})"
                                                title="{{ __('finance::finance.actions.delete') }}">
                                            <span wire:loading.remove wire:target="deleteTransaction({{ $entry->id }})">
                                                <i class="bi bi-trash fs-5"></i>
                                            </span>
                                            <span wire:loading wire:target="deleteTransaction({{ $entry->id }})">
                                                <span class="spinner-border spinner-border-sm"></span>
                                            </span>
                                        </button>
                                    @else
                                        <span class="text-muted fs-8">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

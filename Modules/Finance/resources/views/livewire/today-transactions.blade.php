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
        </div>
        <div class="card-body pt-0">
            @if($entries->isEmpty())
                <p class="text-muted mb-0">{{ __('finance::finance.metrics.no_transactions_today') }}</p>
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
                                <td class="fw-bold">{{ number_format($entry->amount, 2) }} {{ $entry->currency }}</td>
                                <td>{{ $expenseLine?->expenseCategory?->name ?? '—' }}</td>
                                <td class="text-muted">{{ \Illuminate\Support\Str::limit($entry->description, 50) ?: '—' }}</td>
                                <td class="text-muted">{{ $entry->transaction_date?->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

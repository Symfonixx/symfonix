<div>
    <div class="card card-flush border-0 shadow-sm h-100">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('finance::finance.metrics.today_transactions') }}</h3>
        </div>
        <div class="card-body pt-0">
            @if($transactions->isEmpty())
                <p class="text-muted mb-0">{{ __('finance::finance.metrics.no_transactions_today') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3">
                        <thead>
                        <tr class="text-muted fw-semibold fs-7">
                            <th>{{ __('finance::finance.fields.type') }}</th>
                            <th>{{ __('finance::finance.fields.amount') }}</th>
                            <th>{{ __('finance::finance.fields.category') }}</th>
                            <th>{{ __('finance::finance.fields.description') }}</th>
                            <th>{{ __('finance::finance.fields.date') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($transactions as $transaction)
                            <tr wire:key="transaction-{{ $transaction->id }}">
                                <td>
                                    <span class="badge badge-light-{{ $transaction->type === 'income' ? 'success' : 'danger' }}">
                                        {{ $transaction->type === 'income' ? __('finance::finance.fields.income') : __('finance::finance.fields.expense') }}
                                    </span>
                                </td>
                                <td class="fw-bold">{{ number_format($transaction->amount, 2) }}</td>
                                <td>{{ $transaction->expenseCategory?->name ?? '—' }}</td>
                                <td class="text-muted">{{ \Illuminate\Support\Str::limit($transaction->description, 50) ?: '—' }}</td>
                                <td class="text-muted">{{ $transaction->transaction_date?->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

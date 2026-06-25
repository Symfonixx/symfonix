<?php

namespace Modules\Finance\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Finance\Models\Transaction;

class TodayTransactions extends Component
{
    #[On('transaction-logged')]
    public function refreshList(): void
    {
        // Re-render on event.
    }

    public function render()
    {
        $transactions = Transaction::query()
            ->with(['expenseCategory:id,name', 'transactionable'])
            ->today()
            ->latest()
            ->limit(20)
            ->get();

        return view('finance::livewire.today-transactions', [
            'transactions' => $transactions,
        ]);
    }
}

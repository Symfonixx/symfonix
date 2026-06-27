<?php

namespace Modules\Finance\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Finance\Models\JournalEntry;

class TodayTransactions extends Component
{
    #[On('transaction-logged')]
    public function refreshList(): void
    {
        // Re-render on event.
    }

    public function render()
    {
        $entries = JournalEntry::query()
            ->with(['lines.expenseCategory:id,name'])
            ->today()
            ->latest()
            ->limit(20)
            ->get();

        return view('finance::livewire.today-transactions', [
            'entries' => $entries,
        ]);
    }
}

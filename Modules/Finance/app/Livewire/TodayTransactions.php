<?php

namespace Modules\Finance\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Finance\Models\JournalEntry;

class TodayTransactions extends Component
{
    public string $fromDate = '';

    public string $toDate = '';

    public function mount(): void
    {
        $this->fromDate = today()->toDateString();
        $this->toDate = today()->toDateString();
    }

    #[On('transaction-logged')]
    public function refreshList(): void
    {
        // Re-render on event.
    }

    public function resetFilters(): void
    {
        $this->fromDate = today()->toDateString();
        $this->toDate = today()->toDateString();
    }

    public function render()
    {
        $from = $this->fromDate ?: null;
        $to = $this->toDate ?: null;

        if ($from && $to && $from > $to) {
            [$from, $to] = [$to, $from];
        }

        $entries = JournalEntry::query()
            ->with(['lines.expenseCategory:id,name'])
            ->when($from, fn ($query) => $query->whereDate('transaction_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('transaction_date', '<=', $to))
            ->latest()
            ->limit(100)
            ->get();

        return view('finance::livewire.today-transactions', [
            'entries' => $entries,
        ]);
    }
}

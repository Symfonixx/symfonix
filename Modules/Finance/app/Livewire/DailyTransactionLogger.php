<?php

namespace Modules\Finance\Livewire;

use Livewire\Component;
use Modules\Finance\Models\ExpenseCategory;
use Modules\Finance\Services\FinanceService;
use Modules\Project\Models\Project;

class DailyTransactionLogger extends Component
{
    public string $type = 'debit';

    public ?int $expense_category_id = null;

    public string $amount = '';

    public string $description = '';

    public ?int $project_id = null;

    public string $transaction_date = '';

    public string $currency = '';

    public function mount(): void
    {
        $this->transaction_date = now()->toDateString();
        $this->currency = app(\Modules\Finance\Services\CurrencyService::class)->defaultCurrency();
    }

    public function updatedType(): void
    {
        if ($this->type === 'credit') {
            $this->expense_category_id = null;
        }
    }

    public function logTransaction(FinanceService $financeService): void
    {
        $rules = [
            'type' => ['required', 'in:debit,credit'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'description' => ['nullable', 'string', 'max:1000'],
            'transaction_date' => ['required', 'date'],
            'project_id' => ['nullable', 'exists:projects,id'],
        ];

        if ($this->type === 'debit') {
            $rules['expense_category_id'] = ['required', 'exists:expense_categories,id'];
        }

        $this->validate($rules);

        $referenceType = null;
        $referenceId = null;

        if ($this->project_id) {
            $referenceType = Project::class;
            $referenceId = $this->project_id;
        }

        $financeService->logTransaction([
            'type' => $this->type,
            'expense_category_id' => $this->expense_category_id,
            'amount' => $this->amount,
            'currency' => strtoupper($this->currency),
            'description' => $this->description ?: null,
            'transaction_date' => $this->transaction_date,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);

        $this->reset(['amount', 'description', 'expense_category_id', 'project_id']);
        $this->transaction_date = now()->toDateString();

        $this->dispatch('transaction-logged');
        session()->flash('finance_log_success', __('finance::finance.messages.transaction_logged'));
    }

    public function render()
    {
        return view('finance::livewire.daily-transaction-logger', [
            'categories' => ExpenseCategory::query()->orderBy('name')->get(),
            'projects' => Project::query()->orderBy('title')->get(['id', 'title']),
        ]);
    }
}

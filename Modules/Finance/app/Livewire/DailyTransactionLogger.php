<?php

namespace Modules\Finance\Livewire;

use Livewire\Component;
use Modules\Finance\Models\ExpenseCategory;
use Modules\Finance\Models\Transaction;
use Modules\Finance\Services\FinanceService;
use Modules\Project\Models\Project;

class DailyTransactionLogger extends Component
{
    public string $type = Transaction::TYPE_EXPENSE;

    public ?int $expense_category_id = null;

    public string $amount = '';

    public string $description = '';

    public ?int $project_id = null;

    public string $transaction_date = '';

    public function mount(): void
    {
        $this->transaction_date = now()->toDateString();
    }

    public function updatedType(): void
    {
        if ($this->type === Transaction::TYPE_INCOME) {
            $this->expense_category_id = null;
        }
    }

    public function logTransaction(FinanceService $financeService): void
    {
        $rules = [
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:1000'],
            'transaction_date' => ['required', 'date'],
            'project_id' => ['nullable', 'exists:projects,id'],
        ];

        if ($this->type === Transaction::TYPE_EXPENSE) {
            $rules['expense_category_id'] = ['required', 'exists:expense_categories,id'];
        }

        $this->validate($rules);

        $referenceType = null;
        $referenceId = null;

        if ($this->type === Transaction::TYPE_INCOME && $this->project_id) {
            $referenceType = Project::class;
            $referenceId = $this->project_id;
        }

        $financeService->logTransaction([
            'type' => $this->type,
            'expense_category_id' => $this->expense_category_id,
            'amount' => $this->amount,
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

<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Modules\AI\Support\CostLimiter;
use Modules\AI\Support\ToolResult;
use Modules\Finance\Models\JournalLine;
use Modules\Finance\Services\CurrencyService;
use Modules\Finance\Services\FinanceService;

class GetExpenseStatsTool extends AbstractAssistantTool
{
    public function __construct(
        CostLimiter $limiter,
        private readonly FinanceService $financeService,
        private readonly CurrencyService $currencyService,
    ) {
        parent::__construct($limiter);
    }

    public function name(): string
    {
        return 'get_expense_stats';
    }

    public function description(): string
    {
        return 'Get total expenses for a period and the largest expense categories.';
    }

    public function parameters(): array
    {
        return $this->periodParameters();
    }

    public function permissions(): array
    {
        return ['finance.dashboard.view'];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $range = $this->period($arguments);
        $summary = $this->financeService->getMetricsSummary($range['month_keys']);

        $breakdown = JournalLine::query()
            ->selectRaw('expense_category_id, SUM(amount) as total')
            ->where('account', JournalLine::ACCOUNT_EXPENSE)
            ->where('side', JournalLine::SIDE_DEBIT)
            ->whereHas('journalEntry', function ($query) use ($range) {
                $query->expense()->whereBetween('transaction_date', [$range['start'], $range['end']]);
            })
            ->groupBy('expense_category_id')
            ->orderByDesc('total')
            ->limit($this->limiter->maxListItems())
            ->with('expenseCategory:id,name')
            ->get()
            ->map(fn (JournalLine $row) => [
                'category' => $row->expenseCategory?->name ?? __('ai::assistant.uncategorized'),
                'total' => round((float) $row->total, 2),
            ])
            ->all();

        $data = [
            'period' => $range['period'],
            'period_label' => $range['source_label'],
            'currency' => $this->currencyService->displayCurrency(),
            'total_expenses' => round((float) $summary['expenses'], 2),
            'biggest_categories' => $breakdown,
        ];

        return ToolResult::success($data, ['Expenses', $range['source_label']]);
    }
}

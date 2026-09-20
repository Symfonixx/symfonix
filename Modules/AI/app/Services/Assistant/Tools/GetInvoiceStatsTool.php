<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Modules\AI\Support\CostLimiter;
use Modules\AI\Support\ToolResult;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Services\CurrencyService;

class GetInvoiceStatsTool extends AbstractAssistantTool
{
    public function __construct(
        CostLimiter $limiter,
        private readonly CurrencyService $currencyService,
    ) {
        parent::__construct($limiter);
    }

    public function name(): string
    {
        return 'get_invoice_stats';
    }

    public function description(): string
    {
        return 'Get invoice counts and totals, including how many invoices are overdue. Use this for overdue invoice questions.';
    }

    public function parameters(): array
    {
        return $this->periodParameters();
    }

    public function permissions(): array
    {
        return ['finance.invoices.view', 'finance.dashboard.view'];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $range = $this->period($arguments);
        $currency = $this->currencyService->displayCurrency();

        $open = Invoice::query()
            ->open()
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as value')
            ->first();

        $overdue = Invoice::query()
            ->where('status', Invoice::STATUS_OVERDUE)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as value')
            ->first();

        $paid = Invoice::query()
            ->where('status', Invoice::STATUS_PAID)
            ->whereBetween('paid_at', [$range['start'], $range['end']])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as value')
            ->first();

        $overdueList = Invoice::query()
            ->where('status', Invoice::STATUS_OVERDUE)
            ->with('company:id,name')
            ->orderBy('due_at')
            ->limit($this->limiter->maxListItems())
            ->get(['id', 'invoice_number', 'company_id', 'total', 'currency', 'due_at']);

        $data = [
            'period' => $range['period'],
            'period_label' => $range['source_label'],
            'currency' => $currency,
            'open_count' => (int) ($open->count ?? 0),
            'open_total' => round((float) ($open->value ?? 0), 2),
            'overdue_count' => (int) ($overdue->count ?? 0),
            'overdue_total' => round((float) ($overdue->value ?? 0), 2),
            'paid_in_period_count' => (int) ($paid->count ?? 0),
            'paid_in_period_total' => round((float) ($paid->value ?? 0), 2),
            'overdue_invoices' => $overdueList->map(fn (Invoice $invoice) => [
                'id' => $invoice->id,
                'number' => $invoice->invoice_number,
                'company' => $invoice->company?->name,
                'total' => $invoice->total,
                'currency' => $invoice->currency,
                'due_at' => $invoice->due_at?->toDateString(),
            ])->all(),
        ];

        return ToolResult::success($data, ['Invoices', $range['source_label']]);
    }
}

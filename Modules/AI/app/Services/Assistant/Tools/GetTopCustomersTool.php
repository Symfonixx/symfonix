<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Modules\AI\Support\CostLimiter;
use Modules\AI\Support\ToolResult;
use Modules\CRM\Models\Company;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Services\CurrencyService;

class GetTopCustomersTool extends AbstractAssistantTool
{
    public function __construct(
        CostLimiter $limiter,
        private readonly CurrencyService $currencyService,
    ) {
        parent::__construct($limiter);
    }

    public function name(): string
    {
        return 'get_top_customers';
    }

    public function description(): string
    {
        return 'Rank customers (companies) by paid invoice revenue. Use this when asked who the best customers are by revenue or sales.';
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
        $limit = $this->limiter->maxListItems();
        $currency = $this->currencyService->displayCurrency();

        $rows = Invoice::query()
            ->where('status', Invoice::STATUS_PAID)
            ->whereBetween('paid_at', [$range['start'], $range['end']])
            ->selectRaw('company_id, SUM(total) as revenue, COUNT(*) as invoice_count')
            ->groupBy('company_id')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();

        $companies = Company::query()
            ->whereIn('id', $rows->pluck('company_id')->filter())
            ->get(['id', 'name'])
            ->keyBy('id');

        $outstanding = Invoice::query()
            ->open()
            ->whereIn('company_id', $rows->pluck('company_id')->filter())
            ->selectRaw('company_id, COALESCE(SUM(total), 0) as outstanding')
            ->groupBy('company_id')
            ->pluck('outstanding', 'company_id');

        $customers = $rows->map(fn (Invoice $row) => [
            'company_id' => $row->company_id,
            'name' => $companies->get($row->company_id)?->name ?: __('ai::assistant.uncategorized'),
            'revenue' => round((float) $row->revenue, 2),
            'paid_invoices' => (int) $row->invoice_count,
            'outstanding' => round((float) ($outstanding[$row->company_id] ?? 0), 2),
        ])->all();

        return ToolResult::success([
            'period' => $range['period'],
            'period_label' => $range['source_label'],
            'currency' => $currency,
            'top_customers' => $customers,
        ], ['Customers', 'Invoices', $range['source_label']]);
    }
}

<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Modules\AI\Support\ToolResult;
use Modules\CRM\Models\Company;

class GetCustomerStatsTool extends AbstractAssistantTool
{
    public function name(): string
    {
        return 'get_customer_stats';
    }

    public function description(): string
    {
        return 'Get CRM customer (company) totals. For best customers by revenue use get_top_customers.';
    }

    public function parameters(): array
    {
        return $this->periodParameters();
    }

    public function permissions(): array
    {
        return ['crm.companies.view'];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $range = $this->period($arguments);

        $data = [
            'period' => $range['period'],
            'period_label' => $range['source_label'],
            'total' => Company::query()->count(),
            'new_in_period' => Company::query()->whereBetween('created_at', [$range['start'], $range['end']])->count(),
            'active' => Company::query()->where('status', Company::STATUS_ACTIVE)->count(),
            'disabled' => Company::query()->where('status', Company::STATUS_DISABLED)->count(),
        ];

        return ToolResult::success($data, ['Customers', $range['source_label']]);
    }
}

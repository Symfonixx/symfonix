<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Modules\AI\Support\CostLimiter;
use Modules\AI\Support\ToolResult;

class GetBusinessSnapshotTool extends AbstractAssistantTool
{
    public function __construct(
        CostLimiter $limiter,
        private readonly GetLeadStatsTool $leadStats,
        private readonly GetCustomerStatsTool $customerStats,
        private readonly GetTopCustomersTool $topCustomers,
        private readonly GetProjectStatsTool $projectStats,
        private readonly GetInvoiceStatsTool $invoiceStats,
        private readonly GetPaymentStatsTool $paymentStats,
        private readonly GetTicketStatsTool $ticketStats,
        private readonly GetEmployeeStatsTool $employeeStats,
        private readonly GetVisitorStatsTool $visitorStats,
        private readonly GetBestSellingServicesTool $bestSelling,
        private readonly GetEmployeeReportTool $employeeReport,
    ) {
        parent::__construct($limiter);
    }

    public function name(): string
    {
        return 'get_business_snapshot';
    }

    public function description(): string
    {
        return 'Summarize current business status using only domains the user can access, including visitors, services, sales, finance, and employees.';
    }

    public function parameters(): array
    {
        return $this->periodParameters();
    }

    public function permissions(): array
    {
        return [];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $sections = [];
        $sources = [];

        $tools = [
            $this->leadStats,
            $this->customerStats,
            $this->topCustomers,
            $this->projectStats,
            $this->invoiceStats,
            $this->paymentStats,
            $this->ticketStats,
            $this->employeeStats,
            $this->visitorStats,
            $this->bestSelling,
            $this->employeeReport,
        ];

        foreach ($tools as $tool) {
            if (! $tool->authorized($user)) {
                continue;
            }

            $result = $tool->handle($user, $arguments);
            if (! $result->ok || $result->denied) {
                continue;
            }

            $sections[$tool->name()] = $result->data;
            $sources = array_merge($sources, $result->sources);
        }

        if ($sections === []) {
            return ToolResult::denied($this->deniedMessage());
        }

        return ToolResult::success([
            'sections' => $sections,
        ], array_values(array_unique($sources)));
    }
}

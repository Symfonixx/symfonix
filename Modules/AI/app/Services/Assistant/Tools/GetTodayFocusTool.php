<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Modules\AI\Support\CostLimiter;
use Modules\AI\Support\ToolResult;

class GetTodayFocusTool extends AbstractAssistantTool
{
    public function __construct(
        CostLimiter $limiter,
        private readonly GetOverdueWorkTool $overdueWork,
        private readonly GetLeadStatsTool $leadStats,
        private readonly GetInvoiceStatsTool $invoiceStats,
        private readonly GetTicketStatsTool $ticketStats,
        private readonly GetVisitorStatsTool $visitorStats,
    ) {
        parent::__construct($limiter);
    }

    public function name(): string
    {
        return 'get_today_focus';
    }

    public function description(): string
    {
        return 'What to focus on today: overdue work, leads needing follow-up, overdue invoices, open tickets, and today\'s website visits the user can see.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => new \stdClass,
        ];
    }

    public function permissions(): array
    {
        return [];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $focus = [];
        $sources = [];

        foreach ([$this->overdueWork, $this->leadStats, $this->invoiceStats, $this->ticketStats, $this->visitorStats] as $tool) {
            if (! $tool->authorized($user)) {
                continue;
            }

            $result = $tool->handle($user, ['period' => 'today']);
            if (! $result->ok || $result->denied) {
                continue;
            }

            $focus[$tool->name()] = $result->data;
            $sources = array_merge($sources, $result->sources);
        }

        if ($focus === []) {
            return ToolResult::denied($this->deniedMessage());
        }

        return ToolResult::success([
            'focus' => $focus,
        ], array_values(array_unique($sources)));
    }
}

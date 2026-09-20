<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Modules\AI\Support\ToolResult;
use Modules\Support\Enums\TicketStatus;
use Modules\Support\Models\Ticket;

class GetTicketStatsTool extends AbstractAssistantTool
{
    public function name(): string
    {
        return 'get_ticket_stats';
    }

    public function description(): string
    {
        return 'Get support ticket counts by status and a short list of open tickets.';
    }

    public function parameters(): array
    {
        return $this->periodParameters();
    }

    public function permissions(): array
    {
        return ['support.tickets.view'];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $range = $this->period($arguments);
        $byStatus = Ticket::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $open = Ticket::query()
            ->whereIn('status', TicketStatus::openValues())
            ->latest('id')
            ->limit($this->limiter->maxListItems())
            ->get(['id', 'ticket_number', 'subject', 'status', 'priority', 'created_at']);

        $data = [
            'period' => $range['period'],
            'period_label' => $range['source_label'],
            'total' => Ticket::query()->count(),
            'open' => (int) $byStatus->only(TicketStatus::openValues())->sum(),
            'created_in_period' => Ticket::query()->whereBetween('created_at', [$range['start'], $range['end']])->count(),
            'by_status' => $byStatus,
            'open_tickets' => $open->map(fn (Ticket $ticket) => [
                'id' => $ticket->id,
                'number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'created_at' => $ticket->created_at?->toDateString(),
            ])->all(),
        ];

        return ToolResult::success($data, ['Tickets', $range['source_label']]);
    }
}

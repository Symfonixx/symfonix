<?php

namespace Modules\Reporting\Services;

use Illuminate\Support\Facades\DB;
use Modules\Project\Models\Project;
use Modules\Project\Models\ProjectStatus;
use Modules\Reporting\DTOs\ReportFilters;
use Modules\Support\Models\Ticket;
use Modules\Support\Models\TicketCategory;
use Modules\User\Models\AttendanceLog;
use Modules\User\Models\Employee;

class OperationsReportService extends BaseReportService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $resolved = $this->resolveFilters($filters);

        $openTickets = Ticket::query()->whereIn('status', [Ticket::STATUS_OPEN, Ticket::STATUS_IN_PROGRESS])->count();
        $resolvedTickets = $this->resolvedTicketsCount($resolved);
        $prevResolved = $this->resolvedTicketsCount($resolved, previous: true);
        $avgResolution = $this->avgResolutionHours($resolved);
        $attendanceRate = $this->attendanceRate($resolved);
        $activeProjects = $this->activeProjectsCount();

        return [
            'filters' => $resolved->toArray(),
            'chart_colors' => $this->chartColors(),
            'kpis' => [
                'open_tickets' => [
                    'value' => (float) $openTickets,
                    'previous' => (float) $openTickets,
                    'change' => null,
                    'trend' => 'flat',
                ],
                'resolved_tickets' => $this->kpiMetric((float) $resolvedTickets, (float) $prevResolved),
                'avg_resolution_hours' => [
                    'value' => $avgResolution,
                    'previous' => $avgResolution,
                    'change' => null,
                    'trend' => 'flat',
                ],
                'attendance_rate' => [
                    'value' => $attendanceRate,
                    'previous' => $attendanceRate,
                    'change' => null,
                    'trend' => 'flat',
                ],
                'active_projects' => [
                    'value' => (float) $activeProjects,
                    'previous' => (float) $activeProjects,
                    'change' => null,
                    'trend' => 'flat',
                ],
            ],
            'charts' => [
                'ticket_status' => $this->ticketStatusBreakdown(),
                'ticket_by_category' => $this->ticketsByCategory($resolved),
                'ticket_trend' => $this->ticketTrend($resolved),
                'project_status' => $this->projectStatusBreakdown(),
                'attendance_daily' => $this->attendanceDaily($resolved),
            ],
            'tables' => [
                'open_tickets' => $this->openTicketsList(),
                'project_summary' => $this->projectSummary(),
            ],
        ];
    }

    private function resolvedTicketsCount(ReportFilters $filters, bool $previous = false): int
    {
        $start = $previous ? $filters->previousStart : $filters->start;
        $end = $previous ? $filters->previousEnd : $filters->end;

        return Ticket::query()
            ->whereIn('status', [Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED])
            ->whereBetween('closed_at', [$start, $end])
            ->count();
    }

    private function avgResolutionHours(ReportFilters $filters): float
    {
        $avg = Ticket::query()
            ->whereIn('status', [Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED])
            ->whereBetween('closed_at', [$filters->start, $filters->end])
            ->whereNotNull('closed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, closed_at)) as avg_hours')
            ->value('avg_hours');

        return round((float) ($avg ?? 0), 1);
    }

    private function attendanceRate(ReportFilters $filters): float
    {
        $totalEmployees = Employee::query()->count();

        if ($totalEmployees === 0) {
            return 0.0;
        }

        $presentEmployees = AttendanceLog::query()
            ->whereBetween('recorded_at', [$filters->start, $filters->end])
            ->distinct('employee_id')
            ->count('employee_id');

        return round(($presentEmployees / $totalEmployees) * 100, 1);
    }

    /**
     * @return array<int, array{status: string, count: int}>
     */
    private function ticketStatusBreakdown(): array
    {
        return Ticket::query()
            ->select(['status', DB::raw('COUNT(*) as count')])
            ->groupBy('status')
            ->get()
            ->map(fn ($row) => [
                'status' => $row->status,
                'count' => (int) $row->count,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{category: string, count: int}>
     */
    private function ticketsByCategory(ReportFilters $filters): array
    {
        return TicketCategory::query()
            ->withCount(['tickets as period_count' => fn ($q) => $q
                ->whereBetween('created_at', [$filters->start, $filters->end])])
            ->having('period_count', '>', 0)
            ->orderByDesc('period_count')
            ->get()
            ->map(fn (TicketCategory $cat) => [
                'category' => $cat->name,
                'count' => (int) $cat->period_count,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label: string, opened: int, resolved: int}>
     */
    private function ticketTrend(ReportFilters $filters): array
    {
        $opened = Ticket::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as count")
            ->whereBetween('created_at', [$filters->start, $filters->end])
            ->groupBy('period')
            ->pluck('count', 'period');

        $resolved = Ticket::query()
            ->selectRaw("DATE_FORMAT(closed_at, '%Y-%m') as period, COUNT(*) as count")
            ->whereIn('status', [Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED])
            ->whereBetween('closed_at', [$filters->start, $filters->end])
            ->groupBy('period')
            ->pluck('count', 'period');

        $periods = $opened->keys()->merge($resolved->keys())->unique()->sort()->values();

        return $periods->map(fn (string $period) => [
            'label' => $period,
            'opened' => (int) ($opened[$period] ?? 0),
            'resolved' => (int) ($resolved[$period] ?? 0),
        ])->values()->all();
    }

    private function activeProjectsCount(): int
    {
        return Project::query()
            ->whereHas('status', fn ($q) => $q->whereNotIn('name', ['Completed', 'On Hold']))
            ->count();
    }

    /**
     * @return array<int, array{status: string, count: int}>
     */
    private function projectStatusBreakdown(): array
    {
        return ProjectStatus::query()
            ->withCount('projects')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (ProjectStatus $status) => [
                'status' => $status->name,
                'count' => (int) $status->projects_count,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label: string, check_ins: int}>
     */
    private function attendanceDaily(ReportFilters $filters): array
    {
        return AttendanceLog::query()
            ->selectRaw('DATE(recorded_at) as day, COUNT(DISTINCT employee_id) as check_ins')
            ->whereBetween('recorded_at', [$filters->start, $filters->end])
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn ($row) => [
                'label' => $row->day,
                'check_ins' => (int) $row->check_ins,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function openTicketsList(): array
    {
        return Ticket::query()
            ->with(['category:id,name', 'assignee:id,name'])
            ->whereIn('status', [Ticket::STATUS_OPEN, Ticket::STATUS_IN_PROGRESS])
            ->orderByDesc('created_at')
            ->take(10)
            ->get()
            ->map(fn (Ticket $t) => [
                'number' => $t->ticket_number,
                'subject' => $t->subject,
                'priority' => $t->priority,
                'status' => $t->status,
                'category' => $t->category?->name,
                'assignee' => $t->assignee?->name ?? null,
                'created_at' => $t->created_at->toDateString(),
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function projectSummary(): array
    {
        return Project::query()
            ->with(['company:id,name', 'status:id,name'])
            ->whereHas('status', fn ($q) => $q->whereNotIn('name', ['Completed', 'On Hold']))
            ->orderByDesc('updated_at')
            ->take(10)
            ->get()
            ->map(fn (Project $p) => [
                'title' => $p->title,
                'company' => $p->company?->name,
                'status' => $p->status?->name,
                'payment_status' => $p->payment_status,
                'budget' => $p->budget ? (float) $p->budget : null,
            ])
            ->all();
    }
}

<?php

namespace Modules\Reporting\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\PipelineStage;
use Modules\CRM\Services\Analytics\CrmAnalyticsService;
use Modules\Reporting\DTOs\ReportFilters;
use Modules\User\Models\Employee;

class SalesReportService extends BaseReportService
{
    public function __construct(
        private readonly CrmAnalyticsService $crmAnalytics,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $resolved = $this->resolveFilters($filters);
        $currency = $resolved->currency ?? config('crm.default_currency', 'USD');

        $wonDeals = $this->wonDealsQuery($resolved)->get();
        $prevWonDeals = $this->wonDealsQuery($resolved, previous: true)->get();
        $openPipeline = $this->openPipelineValue($resolved);
        $conversionRate = $this->conversionRate($resolved);
        $prevConversionRate = $this->conversionRate($resolved, previous: true);

        $wonValue = (float) $wonDeals->sum('value');
        $prevWonValue = (float) $prevWonDeals->sum('value');

        $analytics = $this->crmAnalytics->build([
            'period' => $resolved->period,
            'date_from' => $resolved->start->toDateString(),
            'date_to' => $resolved->end->toDateString(),
            'assigned_to' => $resolved->assigneeId,
        ]);

        return [
            'filters' => $resolved->toArray(),
            'currency' => $currency,
            'chart_colors' => $this->chartColors(),
            'assignees' => $this->assignees(),
            'kpis' => [
                'pipeline_value' => [
                    'value' => $openPipeline,
                    'previous' => $openPipeline,
                    'change' => null,
                    'trend' => 'flat',
                ],
                'won_deals' => $this->kpiMetric($wonValue, $prevWonValue),
                'won_count' => $this->kpiMetric((float) $wonDeals->count(), (float) $prevWonDeals->count()),
                'conversion_rate' => $this->kpiMetric($conversionRate, $prevConversionRate),
                'avg_deal_value' => $this->kpiMetric(
                    $wonDeals->count() > 0 ? $wonValue / $wonDeals->count() : 0,
                    $prevWonDeals->count() > 0 ? $prevWonValue / $prevWonDeals->count() : 0,
                ),
            ],
            'charts' => [
                'pipeline_funnel' => $analytics['pipeline_funnel'],
                'won_lost_trend' => $this->wonLostTrend($resolved),
                'rep_performance' => $this->repPerformance($resolved),
                'stage_breakdown' => $this->stageBreakdown($resolved),
            ],
            'tables' => [
                'rep_leaderboard' => $analytics['team_leaderboard'],
                'top_deals' => $wonDeals->sortByDesc('value')->take(10)->map(fn (Deal $deal) => [
                    'title' => $deal->title,
                    'company' => $deal->company?->name,
                    'value' => (float) $deal->value,
                    'assignee' => $deal->assignee?->name,
                    'won_at' => $deal->won_at?->toDateString(),
                ])->values()->all(),
            ],
        ];
    }

    private function wonDealsQuery(ReportFilters $filters, bool $previous = false)
    {
        $start = $previous ? $filters->previousStart : $filters->start;
        $end = $previous ? $filters->previousEnd : $filters->end;

        $query = Deal::query()
            ->where('status', Deal::STATUS_WON)
            ->whereBetween('won_at', [$start, $end])
            ->with(['company:id,name', 'assignee:id,name']);

        if ($filters->assigneeId) {
            $query->where('assigned_to', $filters->assigneeId);
        }

        return $query;
    }

    private function openPipelineValue(ReportFilters $filters): float
    {
        $query = Deal::query()->open();

        if ($filters->assigneeId) {
            $query->where('assigned_to', $filters->assigneeId);
        }

        return round((float) $query->sum('value'), 2);
    }

    private function conversionRate(ReportFilters $filters, bool $previous = false): float
    {
        $start = $previous ? $filters->previousStart : $filters->start;
        $end = $previous ? $filters->previousEnd : $filters->end;

        $totalLeads = Lead::query()
            ->whereBetween('created_at', [$start, $end])
            ->when($filters->assigneeId, fn ($q) => $q->where('assigned_to', $filters->assigneeId))
            ->count();

        if ($totalLeads === 0) {
            return 0.0;
        }

        $converted = Lead::query()
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('deal_id')
            ->when($filters->assigneeId, fn ($q) => $q->where('assigned_to', $filters->assigneeId))
            ->count();

        return round(($converted / $totalLeads) * 100, 1);
    }

    /**
     * @return array<int, array{label: string, won: int, lost: int}>
     */
    private function wonLostTrend(ReportFilters $filters): array
    {
        $won = Deal::query()
            ->selectRaw("DATE_FORMAT(won_at, '%Y-%m') as period, COUNT(*) as count")
            ->where('status', Deal::STATUS_WON)
            ->whereBetween('won_at', [$filters->start, $filters->end])
            ->when($filters->assigneeId, fn ($q) => $q->where('assigned_to', $filters->assigneeId))
            ->groupBy('period')
            ->pluck('count', 'period');

        $lost = Deal::query()
            ->selectRaw("DATE_FORMAT(lost_at, '%Y-%m') as period, COUNT(*) as count")
            ->where('status', Deal::STATUS_LOST)
            ->whereBetween('lost_at', [$filters->start, $filters->end])
            ->when($filters->assigneeId, fn ($q) => $q->where('assigned_to', $filters->assigneeId))
            ->groupBy('period')
            ->pluck('count', 'period');

        $periods = $won->keys()->merge($lost->keys())->unique()->sort()->values();

        return $periods->map(fn (string $period) => [
            'label' => $period,
            'won' => (int) ($won[$period] ?? 0),
            'lost' => (int) ($lost[$period] ?? 0),
        ])->values()->all();
    }

    /**
     * @return array<int, array{name: string, won_value: float, won_count: int}>
     */
    private function repPerformance(ReportFilters $filters): array
    {
        return Deal::query()
            ->select([
                'assigned_to',
                DB::raw('COUNT(*) as won_count'),
                DB::raw('COALESCE(SUM(value), 0) as won_value'),
            ])
            ->where('status', Deal::STATUS_WON)
            ->whereBetween('won_at', [$filters->start, $filters->end])
            ->whereNotNull('assigned_to')
            ->groupBy('assigned_to')
            ->orderByDesc('won_value')
            ->with('assignee:id,name')
            ->get()
            ->map(fn ($row) => [
                'name' => $row->assignee?->name ?? __('reporting::report.unassigned'),
                'won_value' => round((float) $row->won_value, 2),
                'won_count' => (int) $row->won_count,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{stage: string, count: int, value: float}>
     */
    private function stageBreakdown(ReportFilters $filters): array
    {
        return PipelineStage::query()
            ->withCount(['deals as open_count' => fn ($q) => $q
                ->open()
                ->when($filters->assigneeId, fn ($q2) => $q2->where('assigned_to', $filters->assigneeId))])
            ->withSum(['deals as open_value' => fn ($q) => $q
                ->open()
                ->when($filters->assigneeId, fn ($q2) => $q2->where('assigned_to', $filters->assigneeId))], 'value')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (PipelineStage $stage) => [
                'stage' => $stage->name,
                'count' => (int) $stage->open_count,
                'value' => round((float) ($stage->open_value ?? 0), 2),
            ])
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, Employee>
     */
    private function assignees()
    {
        return Employee::query()
            ->assignable()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }
}

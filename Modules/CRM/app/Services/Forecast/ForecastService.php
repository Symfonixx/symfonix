<?php

namespace Modules\CRM\Services\Forecast;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\PipelineStage;
use Modules\User\Models\Employee;

class ForecastService
{
    private const HIGH_CONFIDENCE_THRESHOLD = 75;

    public function build(array $filters = []): array
    {
        $resolved = $this->resolveFilters($filters);
        $currency = config('crm.default_currency', 'USD');

        return [
            'filters' => $resolved,
            'summary' => $this->summaryMetrics($resolved, $currency),
            'stage_breakdown' => $this->stageBreakdown($resolved),
            'monthly_predictions' => $this->monthlyPredictions($resolved),
            'assignees' => $this->assignees(),
            'stages' => $this->stages(),
            'currency' => $currency,
            'chart_colors' => config('reporting.chart_colors', config('crm.chart_colors', [
                '#3E97FF', '#50CD89', '#FFC700', '#7239EA', '#F1416C', '#181C32', '#A1A5B7',
            ])),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveFilters(array $filters): array
    {
        $horizonMonths = max(1, min(24, (int) ($filters['horizon'] ?? 6)));
        $start = ! empty($filters['date_from'])
            ? Carbon::parse($filters['date_from'])->startOfDay()
            : Carbon::now()->startOfMonth();
        $end = ! empty($filters['date_to'])
            ? Carbon::parse($filters['date_to'])->endOfDay()
            : Carbon::now()->startOfMonth()->addMonths($horizonMonths - 1)->endOfMonth();

        return [
            'assigned_to' => ! empty($filters['assigned_to']) ? (int) $filters['assigned_to'] : null,
            'pipeline_stage_id' => ! empty($filters['pipeline_stage_id']) ? (int) $filters['pipeline_stage_id'] : null,
            'date_from' => $start->toDateString(),
            'date_to' => $end->toDateString(),
            'horizon' => $horizonMonths,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function summaryMetrics(array $filters, string $currency): array
    {
        $probExpr = $this->probabilityExpression();
        $weightedExpr = $this->weightedValueExpression();
        $threshold = self::HIGH_CONFIDENCE_THRESHOLD;

        $row = $this->baseQuery($filters)
            ->selectRaw("
                COUNT(*) as deal_count,
                COALESCE(SUM(deals.value), 0) as total_pipeline,
                COALESCE(SUM({$weightedExpr}), 0) as forecasted_revenue,
                COALESCE(SUM(CASE WHEN {$probExpr} >= {$threshold} THEN deals.value ELSE 0 END), 0) as best_case,
                COALESCE(SUM(CASE WHEN {$probExpr} < {$threshold} THEN {$weightedExpr} ELSE 0 END), 0) as worst_case
            ")
            ->first();

        return [
            'deal_count' => (int) ($row->deal_count ?? 0),
            'total_pipeline' => [
                'value' => round((float) ($row->total_pipeline ?? 0), 2),
                'currency' => $currency,
            ],
            'forecasted_revenue' => [
                'value' => round((float) ($row->forecasted_revenue ?? 0), 2),
                'currency' => $currency,
            ],
            'best_case' => [
                'value' => round((float) ($row->best_case ?? 0), 2),
                'currency' => $currency,
            ],
            'worst_case' => [
                'value' => round((float) ($row->worst_case ?? 0), 2),
                'currency' => $currency,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return list<array<string, mixed>>
     */
    private function stageBreakdown(array $filters): array
    {
        $weightedExpr = $this->weightedValueExpression();

        $rows = $this->baseQuery($filters)
            ->selectRaw("
                pipeline_stages.id,
                pipeline_stages.name,
                pipeline_stages.slug,
                pipeline_stages.color,
                pipeline_stages.sort_order,
                pipeline_stages.probability as stage_probability,
                COUNT(*) as deal_count,
                COALESCE(SUM(deals.value), 0) as unweighted_value,
                COALESCE(SUM({$weightedExpr}), 0) as weighted_value
            ")
            ->groupBy(
                'pipeline_stages.id',
                'pipeline_stages.name',
                'pipeline_stages.slug',
                'pipeline_stages.color',
                'pipeline_stages.sort_order',
                'pipeline_stages.probability',
            )
            ->orderBy('pipeline_stages.sort_order')
            ->get();

        return $rows->map(function ($row) {
            $slug = str_replace('-', '_', (string) $row->slug);
            $key = 'crm::deal.stages.'.$slug;

            return [
                'id' => (int) $row->id,
                'name' => __($key) !== $key ? __($key) : (string) $row->name,
                'color' => (string) $row->color,
                'stage_probability' => (int) $row->stage_probability,
                'deal_count' => (int) $row->deal_count,
                'unweighted_value' => round((float) $row->unweighted_value, 2),
                'weighted_value' => round((float) $row->weighted_value, 2),
            ];
        })->values()->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return list<array<string, mixed>>
     */
    private function monthlyPredictions(array $filters): array
    {
        $monthExpr = $this->monthExpression('deals.expected_close_date');
        $weightedExpr = $this->weightedValueExpression();

        $rows = $this->baseQuery($filters)
            ->whereNotNull('deals.expected_close_date')
            ->selectRaw("
                {$monthExpr} as month_key,
                COUNT(*) as deal_count,
                COALESCE(SUM(deals.value), 0) as unweighted_value,
                COALESCE(SUM({$weightedExpr}), 0) as weighted_value
            ")
            ->groupByRaw($monthExpr)
            ->orderBy('month_key')
            ->get()
            ->keyBy('month_key');

        $months = $this->monthRange($filters['date_from'], $filters['date_to']);

        return collect($months)->map(function (string $monthKey) use ($rows) {
            $row = $rows->get($monthKey);
            $date = Carbon::createFromFormat('Y-m', $monthKey);

            return [
                'month' => $monthKey,
                'label' => $date->format('M Y'),
                'deal_count' => (int) ($row->deal_count ?? 0),
                'unweighted_value' => round((float) ($row->unweighted_value ?? 0), 2),
                'weighted_value' => round((float) ($row->weighted_value ?? 0), 2),
            ];
        })->values()->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function baseQuery(array $filters)
    {
        $query = Deal::query()
            ->visibleTo()
            ->open()
            ->join('pipeline_stages', 'pipeline_stages.id', '=', 'deals.pipeline_stage_id')
            ->where('pipeline_stages.is_won', false)
            ->where('pipeline_stages.is_lost', false);

        if (! empty($filters['assigned_to'])) {
            $query->where('deals.assigned_to', (int) $filters['assigned_to']);
        }

        if (! empty($filters['pipeline_stage_id'])) {
            $query->where('deals.pipeline_stage_id', (int) $filters['pipeline_stage_id']);
        }

        $query->whereNotNull('deals.expected_close_date');

        if (! empty($filters['date_from'])) {
            $query->where('deals.expected_close_date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('deals.expected_close_date', '<=', $filters['date_to']);
        }

        return $query;
    }

    private function probabilityExpression(): string
    {
        return 'COALESCE(deals.probability, pipeline_stages.probability, 0)';
    }

    private function weightedValueExpression(): string
    {
        $prob = $this->probabilityExpression();

        return "deals.value * ({$prob} / 100)";
    }

    private function monthExpression(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', {$column})",
            'pgsql' => "TO_CHAR({$column}, 'YYYY-MM')",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }

    /**
     * @return list<string>
     */
    private function monthRange(string $from, string $to): array
    {
        $start = Carbon::parse($from)->startOfMonth();
        $end = Carbon::parse($to)->startOfMonth();
        $months = [];

        while ($start->lte($end)) {
            $months[] = $start->format('Y-m');
            $start->addMonth();
        }

        return $months;
    }

    private function assignees(): Collection
    {
        return Employee::query()
            ->assignable()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function stages(): array
    {
        return PipelineStage::query()
            ->active()
            ->ordered()
            ->where('is_won', false)
            ->where('is_lost', false)
            ->get(['id', 'name', 'slug', 'color', 'probability'])
            ->map(fn (PipelineStage $stage) => [
                'id' => $stage->id,
                'name' => $stage->display_name,
                'color' => $stage->color,
                'probability' => $stage->probability,
            ])
            ->values()
            ->all();
    }
}

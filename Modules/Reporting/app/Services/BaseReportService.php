<?php

namespace Modules\Reporting\Services;

use Modules\CRM\Support\DateRangeResolver;
use Modules\Reporting\DTOs\ReportFilters;

abstract class BaseReportService
{
    /**
     * @return list<string>
     */
    protected function chartColors(): array
    {
        return config('reporting.chart_colors', [
            '#3E97FF', '#50CD89', '#FFC700', '#7239EA', '#F1416C', '#181C32', '#A1A5B7',
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function resolveFilters(array $filters): ReportFilters
    {
        $period = $filters['period'] ?? config('reporting.default_period', 'this_month');
        $range = DateRangeResolver::resolve(
            $period,
            $filters['date_from'] ?? null,
            $filters['date_to'] ?? null,
        );

        return new ReportFilters(
            period: $period,
            start: $range['start'],
            end: $range['end'],
            previousStart: $range['previous_start'],
            previousEnd: $range['previous_end'],
            label: $range['label'],
            assigneeId: ! empty($filters['assigned_to']) ? (int) $filters['assigned_to'] : null,
            employeeId: ! empty($filters['employee_id']) ? (int) $filters['employee_id'] : null,
            categoryId: ! empty($filters['category_id']) ? (int) $filters['category_id'] : null,
            currency: $filters['currency'] ?? config('crm.default_currency', 'USD'),
        );
    }

    protected function percentChange(float $current, float $previous): ?float
    {
        if ($previous == 0.0) {
            return $current > 0 ? 100.0 : null;
        }

        return round((($current - $previous) / abs($previous)) * 100, 1);
    }

    protected function trendDirection(float $current, float $previous): string
    {
        if ($current > $previous) {
            return 'up';
        }

        if ($current < $previous) {
            return 'down';
        }

        return 'flat';
    }

    /**
     * @return array{value: float, previous: float, change: ?float, trend: string}
     */
    protected function kpiMetric(float $current, float $previous): array
    {
        return [
            'value' => round($current, 2),
            'previous' => round($previous, 2),
            'change' => $this->percentChange($current, $previous),
            'trend' => $this->trendDirection($current, $previous),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    abstract public function build(array $filters = []): array;
}

<?php

namespace Modules\CRM\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class DateRangeResolver
{
    public static function resolve(string $period, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $end = now()->endOfDay();

        [$start, $label] = match ($period) {
            'today' => [now()->startOfDay(), 'today'],
            'last_7_days' => [now()->subDays(6)->startOfDay(), 'last_7_days'],
            'this_month' => [now()->startOfMonth(), 'this_month'],
            'last_month' => [
                now()->subMonth()->startOfMonth(),
                'last_month',
            ],
            'this_quarter' => [now()->firstOfQuarter(), 'this_quarter'],
            'this_year' => [now()->startOfYear(), 'this_year'],
            'custom' => [
                Carbon::parse($dateFrom ?? now()->subDays(29))->startOfDay(),
                'custom',
            ],
            default => [now()->startOfMonth(), 'this_month'],
        };

        if ($period === 'last_month') {
            $end = now()->subMonth()->endOfMonth()->endOfDay();
        }

        if ($period === 'custom' && $dateTo) {
            $end = Carbon::parse($dateTo)->endOfDay();
        }

        $days = max(1, $start->diffInDays($end) + 1);
        $previousEnd = $start->copy()->subDay()->endOfDay();
        $previousStart = $previousEnd->copy()->subDays($days - 1)->startOfDay();

        return [
            'period' => $period,
            'label' => $label,
            'start' => $start,
            'end' => $end,
            'previous_start' => $previousStart,
            'previous_end' => $previousEnd,
        ];
    }

    public static function between(CarbonInterface $start, CarbonInterface $end): \Closure
    {
        return fn ($query, string $column = 'created_at') => $query->whereBetween($column, [$start, $end]);
    }
}

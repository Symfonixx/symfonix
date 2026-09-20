<?php

namespace Modules\AI\Support;

use Carbon\Carbon;
use Modules\CRM\Support\DateRangeResolver;

final class AssistantPeriod
{
    /**
     * @return list<string>
     */
    public static function allowed(): array
    {
        return ['today', 'last_7_days', 'this_month', 'last_month', 'this_quarter', 'this_year'];
    }

    /**
     * @return array{period: string, label: string, start: Carbon, end: Carbon, previous_start: Carbon, previous_end: Carbon, month_keys: list<string>, source_label: string}
     */
    public static function resolve(?string $period = null): array
    {
        $period = in_array($period, self::allowed(), true) ? $period : 'this_month';
        $range = DateRangeResolver::resolve($period);

        $monthKeys = [];
        $cursor = $range['start']->copy()->startOfMonth();
        $end = $range['end']->copy()->startOfMonth();
        while ($cursor->lte($end)) {
            $monthKeys[] = $cursor->format('Y-m');
            $cursor->addMonth();
        }

        $range['month_keys'] = $monthKeys;
        $range['source_label'] = $range['start']->translatedFormat('F Y');

        return $range;
    }

    /**
     * @return array<string, mixed>
     */
    public static function schemaProperty(): array
    {
        return [
            'type' => 'string',
            'enum' => self::allowed(),
            'description' => 'Time period. Defaults to this_month.',
        ];
    }
}

<?php

namespace MonishRoy\VisitorTracking\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use MonishRoy\VisitorTracking\Models\VisitorTable;

class Visitor
{
    public static function totalVisitors()
    {
        return VisitorTable::count();
    }

    public static function uniqueVisitors()
    {
        return VisitorTable::distinct('ip')->count('ip');
    }

    public static function topVisitedPages($limit = 5)
    {
        return VisitorTable::select('url', 'page_title', DB::raw('count(*) as total'))
            ->groupBy('url', 'page_title')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    public static function topReferrers(int $limit = 10)
    {
        if (! Schema::hasColumn('visitors', 'referrer')) {
            return collect();
        }

        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        return VisitorTable::select('referrer', DB::raw('count(*) as total'))
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->when($appHost, function ($query) use ($appHost) {
                $query->where('referrer', 'not like', '%'.$appHost.'%');
            })
            ->groupBy('referrer')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    public static function visitsByMonth(int $months = 12)
    {
        $start = now()->subMonths($months - 1)->startOfMonth();

        $rows = VisitorTable::query()
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', $start)
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $result = collect();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');

            $result->push([
                'month' => $key,
                'label' => $date->translatedFormat('M Y'),
                'total' => (int) ($rows[$key]->total ?? 0),
            ]);
        }

        return $result;
    }

    public static function countries()
    {
        return VisitorTable::select('country', DB::raw('count(*) as total'))
            ->groupBy('country')
            ->orderByDesc('total')
            ->get();
    }

    public static function os()
    {
        return VisitorTable::select('os', DB::raw('count(*) as total'))
            ->groupBy('os')
            ->get();
    }

    public static function devices()
    {
        return VisitorTable::select('device', DB::raw('count(*) as total'))
            ->groupBy('device')
            ->get();
    }
}

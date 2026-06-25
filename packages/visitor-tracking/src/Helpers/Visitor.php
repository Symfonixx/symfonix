<?php

namespace MonishRoy\VisitorTracking\Helpers;

use MonishRoy\VisitorTracking\Models\VisitorTable;
use Illuminate\Support\Facades\DB;

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

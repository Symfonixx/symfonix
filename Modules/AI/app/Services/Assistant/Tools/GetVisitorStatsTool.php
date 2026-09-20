<?php

namespace Modules\AI\Services\Assistant\Tools;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\AI\Support\ToolResult;
use MonishRoy\VisitorTracking\Models\VisitorTable;

class GetVisitorStatsTool extends AbstractAssistantTool
{
    public function name(): string
    {
        return 'get_visitor_stats';
    }

    public function description(): string
    {
        return 'Get website visit and unique-visitor counts for a period, plus top pages and referrers.';
    }

    public function parameters(): array
    {
        return $this->periodParameters();
    }

    public function permissions(): array
    {
        return ['support.visitors.view', 'overview.dashboard.view'];
    }

    protected function execute(User $user, array $arguments): ToolResult
    {
        $range = $this->period($arguments);
        $limit = $this->limiter->maxListItems();

        $periodQuery = VisitorTable::query()->whereBetween('created_at', [$range['start'], $range['end']]);

        $topPages = (clone $periodQuery)
            ->select('url', 'page_title', DB::raw('COUNT(*) as total'))
            ->groupBy('url', 'page_title')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'title' => $row->page_title ?: $row->url,
                'url' => $row->url,
                'visits' => (int) $row->total,
            ])
            ->all();

        $topReferrers = [];
        if ($user->can('support.visitors.view') && Schema::hasColumn('visitors', 'referrer')) {
            $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
            $topReferrers = (clone $periodQuery)
                ->select('referrer', DB::raw('COUNT(*) as total'))
                ->whereNotNull('referrer')
                ->where('referrer', '!=', '')
                ->when($appHost, fn ($query) => $query->where('referrer', 'not like', '%'.$appHost.'%'))
                ->groupBy('referrer')
                ->orderByDesc('total')
                ->limit($limit)
                ->get()
                ->map(fn ($row) => [
                    'referrer' => $row->referrer,
                    'visits' => (int) $row->total,
                ])
                ->all();
        }

        $data = [
            'period' => $range['period'],
            'period_label' => $range['source_label'],
            'hits_in_period' => (clone $periodQuery)->count(),
            'unique_visitors_in_period' => (clone $periodQuery)->distinct('ip')->count('ip'),
            'unique_visitors_today' => VisitorTable::query()->whereDate('created_at', today())->distinct('ip')->count('ip'),
            'unique_visitors_all_time' => VisitorTable::query()->distinct('ip')->count('ip'),
            'top_pages' => $topPages,
            'top_referrers' => $topReferrers,
        ];

        return ToolResult::success($data, ['Website visits', $range['source_label']]);
    }
}

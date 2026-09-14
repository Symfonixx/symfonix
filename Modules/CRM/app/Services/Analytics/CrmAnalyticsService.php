<?php

namespace Modules\CRM\Services\Analytics;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Models\CrmAuditLog;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\PipelineStage;
use Modules\CRM\Services\SalesTarget\SalesTargetService;
use Modules\CRM\Support\CrmSubjectResolver;
use Modules\CRM\Support\DateRangeResolver;
use Modules\Finance\Models\Invoice;
use Modules\User\Models\Employee;

class CrmAnalyticsService
{
    private const IN_PROGRESS_LEAD_STATUSES = [
        Lead::STATUS_NEW,
        Lead::STATUS_CONTACTED,
        Lead::STATUS_QUALIFIED,
    ];

    public function build(array $filters = []): array
    {
        $period = $filters['period'] ?? 'this_month';
        $assigneeId = ! empty($filters['assigned_to']) ? (int) $filters['assigned_to'] : null;
        $range = DateRangeResolver::resolve($period, $filters['date_from'] ?? null, $filters['date_to'] ?? null);
        $currency = config('crm.default_currency', 'USD');

        $summary = $this->attachSparklines(
            $this->summaryMetrics($range, $assigneeId, $currency),
            $assigneeId,
        );
        $pipelineFunnel = $this->pipelineFunnel($assigneeId);
        $openLeadsByStages = $this->openLeadsByStages($assigneeId);
        $leadChannels = $this->leadChannels($range, $assigneeId);
        $teamLeaderboard = $this->teamLeaderboard($range, $assigneeId);
        $recentActivity = $this->recentActivity($assigneeId);
        $topCustomers = $this->topCustomers($assigneeId);

        return [
            'filters' => [
                'period' => $period,
                'assigned_to' => $assigneeId,
                'date_from' => $range['start']->toDateString(),
                'date_to' => $range['end']->toDateString(),
                'label' => $range['label'],
            ],
            'summary' => $summary,
            'pipeline_funnel' => $pipelineFunnel,
            'open_leads_by_stages' => $openLeadsByStages,
            'lead_channels' => $leadChannels,
            'team_leaderboard' => $teamLeaderboard,
            'recent_activity' => $recentActivity,
            'top_customers' => $topCustomers,
            'widgets' => $this->widgetDataMap($summary, $pipelineFunnel, $openLeadsByStages, $leadChannels, $teamLeaderboard, $recentActivity, $topCustomers),
            'assignees' => $this->assignees(),
            'chart_colors' => config('reporting.chart_colors', config('crm.chart_colors', [
                '#3E97FF', '#50CD89', '#FFC700', '#7239EA', '#F1416C', '#181C32', '#A1A5B7',
            ])),
            'currency' => $currency,
        ];
    }

    private function widgetDataMap(
        array $summary,
        array $pipelineFunnel,
        array $openLeadsByStages,
        array $leadChannels,
        array $teamLeaderboard,
        array $recentActivity,
        array $topCustomers,
    ): array {
        return array_merge($summary, [
            'pipeline_funnel' => $pipelineFunnel,
            'open_leads_by_stages' => $openLeadsByStages,
            'lead_channels' => $leadChannels,
            'sales_performance' => $teamLeaderboard,
            'recent_activity' => $recentActivity,
            'top_customers' => $topCustomers,
        ]);
    }

    private function summaryMetrics(array $range, ?int $assigneeId, string $currency): array
    {
        $companies = $this->companyMetricValues($range);
        $leads = $this->leadMetricValues($range, $assigneeId);
        $deals = $this->dealMetricValues($range, $assigneeId);
        $invoiceMetrics = $this->invoiceMetrics($currency);

        $totalCustomers = $companies['total'];
        $previousCustomers = $companies['previous_total'];
        $newCustomers = $companies['new'];
        $previousNewCustomers = $companies['previous_new'];
        $activeCustomers = $companies['active'];
        $lostCustomers = $companies['lost'];

        $currentLeads = $leads['current'];
        $previousLeads = $leads['previous'];
        $leadsInProgress = $leads['in_progress'];
        $totalLeadsAll = $leads['total'];
        $convertedToWon = $leads['converted'];
        $conversionRate = $leads['conversion_rate'];
        $previousConverted = $leads['previous_converted'];
        $currentConverted = $leads['current_converted'];

        $pipelineValue = $deals['pipeline'];
        $previousPipelineValue = $deals['previous_pipeline'];
        $wonDealsCount = $deals['won_count'];
        $wonDealsValue = $deals['won_value'];
        $previousWonCount = $deals['previous_won_count'];
        $lostDealsCount = $deals['lost_count'];
        $lostDealsValue = $deals['lost_value'];
        $previousLostCount = $deals['previous_lost_count'];
        $totalSales = $deals['total_sales'];
        $salesThisMonth = $deals['sales_this_month'];
        $previousSalesThisMonth = $deals['previous_sales_this_month'];
        $avgDealValue = $deals['avg_deal_value'];
        $avgCloseTime = $deals['avg_close_time'];

        return [
            'total_customers' => [
                'value' => $totalCustomers,
                'trend' => $this->trend($totalCustomers, $previousCustomers),
            ],
            'new_customers' => [
                'value' => $newCustomers,
                'trend' => $this->trend($newCustomers, $previousNewCustomers),
            ],
            'new_leads' => [
                'value' => $currentLeads,
                'trend' => $this->trend($currentLeads, $previousLeads),
            ],
            'total_leads' => [
                'value' => $totalLeadsAll,
                'trend' => $this->trend($currentLeads, $previousLeads),
            ],
            'leads_in_progress' => [
                'value' => $leadsInProgress,
                'trend' => 0,
            ],
            'active_customers' => [
                'value' => $activeCustomers,
                'trend' => 0,
            ],
            'lost_customers' => [
                'value' => $lostCustomers,
                'trend' => 0,
            ],
            'total_sales' => [
                'value' => $totalSales,
                'trend' => 0,
                'currency' => $currency,
            ],
            'sales_this_month' => [
                'value' => $salesThisMonth,
                'trend' => $this->trend($salesThisMonth, $previousSalesThisMonth),
                'currency' => $currency,
            ],
            'conversion_rate' => [
                'value' => $conversionRate,
                'trend' => $this->trend($currentConverted, max(1, $previousConverted)),
                'converted' => $convertedToWon,
                'total' => $totalLeadsAll,
                'progress' => (int) min(100, round($conversionRate)),
            ],
            'pipeline_value' => [
                'value' => $pipelineValue,
                'trend' => $this->trend($pipelineValue, $previousPipelineValue),
                'currency' => $currency,
            ],
            'won_deals' => [
                'count' => $wonDealsCount,
                'value' => $wonDealsValue,
                'trend' => $this->trend($wonDealsCount, $previousWonCount),
                'currency' => $currency,
            ],
            'lost_deals' => [
                'count' => $lostDealsCount,
                'value' => $lostDealsValue,
                'trend' => $this->trend($lostDealsCount, $previousLostCount),
                'currency' => $currency,
            ],
            'avg_deal_value' => [
                'value' => $avgDealValue,
                'trend' => 0,
                'currency' => $currency,
            ],
            'avg_close_time' => [
                'value' => $avgCloseTime,
                'trend' => 0,
                'unit' => 'days',
            ],
            'outstanding_invoices' => $invoiceMetrics['outstanding'],
            'overdue_amounts' => $invoiceMetrics['overdue'],
        ];
    }

    /**
     * @return array{total: int, previous_total: int, new: int, previous_new: int, active: int, lost: int}
     */
    private function companyMetricValues(array $range): array
    {
        return [
            'total' => Company::query()->count(),
            'previous_total' => Company::query()->where('created_at', '<=', $range['previous_end'])->count(),
            'new' => Company::query()->whereBetween('created_at', [$range['start'], $range['end']])->count(),
            'previous_new' => Company::query()
                ->whereBetween('created_at', [$range['previous_start'], $range['previous_end']])
                ->count(),
            'active' => Company::query()->where('status', Company::STATUS_ACTIVE)->count(),
            'lost' => Company::query()->where('status', Company::STATUS_DISABLED)->count(),
        ];
    }

    /**
     * @return array{
     *     current: int,
     *     previous: int,
     *     in_progress: int,
     *     total: int,
     *     converted: int,
     *     conversion_rate: float,
     *     previous_converted: int,
     *     current_converted: int
     * }
     */
    private function leadMetricValues(array $range, ?int $assigneeId): array
    {
        $current = $this->leadsQuery($assigneeId)
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->count();
        $previous = $this->leadsQuery($assigneeId)
            ->whereBetween('created_at', [$range['previous_start'], $range['previous_end']])
            ->count();
        $total = $this->leadsQuery($assigneeId)->count();
        $converted = $this->leadsQuery($assigneeId)
            ->whereNotNull('deal_id')
            ->whereHas('deal', fn ($q) => $q->where('status', Deal::STATUS_WON))
            ->count();

        return [
            'current' => $current,
            'previous' => $previous,
            'in_progress' => $this->leadsQuery($assigneeId)
                ->whereIn('status', self::IN_PROGRESS_LEAD_STATUSES)
                ->count(),
            'total' => $total,
            'converted' => $converted,
            'conversion_rate' => $total > 0 ? round(($converted / $total) * 100, 1) : 0.0,
            'previous_converted' => $this->leadsQuery($assigneeId)
                ->whereNotNull('deal_id')
                ->whereHas('deal', fn ($q) => $q
                    ->where('status', Deal::STATUS_WON)
                    ->whereBetween('won_at', [$range['previous_start'], $range['previous_end']]))
                ->count(),
            'current_converted' => $this->leadsQuery($assigneeId)
                ->whereNotNull('deal_id')
                ->whereHas('deal', fn ($q) => $q
                    ->where('status', Deal::STATUS_WON)
                    ->whereBetween('won_at', [$range['start'], $range['end']]))
                ->count(),
        ];
    }

    /**
     * @return array{
     *     pipeline: float,
     *     previous_pipeline: float,
     *     won_count: int,
     *     won_value: float,
     *     previous_won_count: int,
     *     lost_count: int,
     *     lost_value: float,
     *     previous_lost_count: int,
     *     total_sales: float,
     *     sales_this_month: float,
     *     previous_sales_this_month: float,
     *     avg_deal_value: float,
     *     avg_close_time: float
     * }
     */
    private function dealMetricValues(array $range, ?int $assigneeId): array
    {
        $won = $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_WON)
            ->whereBetween('won_at', [$range['start'], $range['end']])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(value), 0) as value')
            ->first();

        $lost = $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_LOST)
            ->whereBetween('lost_at', [$range['start'], $range['end']])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(value), 0) as value')
            ->first();

        $wonCount = (int) ($won->count ?? 0);
        $wonValue = (float) ($won->value ?? 0);

        return [
            'pipeline' => (float) $this->dealsQuery($assigneeId)
                ->where('status', Deal::STATUS_OPEN)
                ->sum('value'),
            'previous_pipeline' => (float) $this->dealsQuery($assigneeId)
                ->where('status', Deal::STATUS_OPEN)
                ->where('created_at', '<=', $range['previous_end'])
                ->sum('value'),
            'won_count' => $wonCount,
            'won_value' => $wonValue,
            'previous_won_count' => $this->dealsQuery($assigneeId)
                ->where('status', Deal::STATUS_WON)
                ->whereBetween('won_at', [$range['previous_start'], $range['previous_end']])
                ->count(),
            'lost_count' => (int) ($lost->count ?? 0),
            'lost_value' => (float) ($lost->value ?? 0),
            'previous_lost_count' => $this->dealsQuery($assigneeId)
                ->where('status', Deal::STATUS_LOST)
                ->whereBetween('lost_at', [$range['previous_start'], $range['previous_end']])
                ->count(),
            'total_sales' => (float) $this->dealsQuery($assigneeId)
                ->where('status', Deal::STATUS_WON)
                ->sum('value'),
            'sales_this_month' => (float) $this->dealsQuery($assigneeId)
                ->where('status', Deal::STATUS_WON)
                ->whereBetween('won_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                ->sum('value'),
            'previous_sales_this_month' => (float) $this->dealsQuery($assigneeId)
                ->where('status', Deal::STATUS_WON)
                ->whereBetween('won_at', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth(),
                ])
                ->sum('value'),
            'avg_deal_value' => $wonCount > 0 ? round($wonValue / $wonCount, 2) : 0.0,
            'avg_close_time' => $this->averageCloseDays($range, $assigneeId),
        ];
    }

    private function averageCloseDays(array $range, ?int $assigneeId): float
    {
        $driver = DB::connection()->getDriverName();
        $closeExpr = 'COALESCE(won_at, closed_at)';

        $daysExpr = match ($driver) {
            'sqlite' => "JULIANDAY({$closeExpr}) - JULIANDAY(created_at)",
            'pgsql' => "EXTRACT(EPOCH FROM ({$closeExpr} - created_at)) / 86400",
            default => "DATEDIFF({$closeExpr}, created_at)",
        };

        $avg = $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_WON)
            ->whereBetween('won_at', [$range['start'], $range['end']])
            ->where(function ($q) {
                $q->whereNotNull('won_at')->orWhereNotNull('closed_at');
            })
            ->selectRaw("AVG({$daysExpr}) as avg_days")
            ->value('avg_days');

        return $avg !== null ? round((float) $avg, 1) : 0.0;
    }

    /**
     * @return array{outstanding: array<string, mixed>, overdue: array<string, mixed>}
     */
    private function invoiceMetrics(string $currency): array
    {
        $outstanding = Invoice::query()
            ->open()
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as value')
            ->first();

        $overdue = Invoice::query()
            ->where('status', Invoice::STATUS_OVERDUE)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as value')
            ->first();

        $outstandingCount = (int) ($outstanding->count ?? 0);
        $outstandingValue = (float) ($outstanding->value ?? 0);
        $overdueCount = (int) ($overdue->count ?? 0);
        $overdueValue = (float) ($overdue->value ?? 0);

        return [
            'outstanding' => [
                'count' => $outstandingCount,
                'value' => $outstandingValue,
                'trend' => 0,
                'currency' => $currency,
            ],
            'overdue' => [
                'count' => $overdueCount,
                'value' => $overdueValue,
                'trend' => 0,
                'currency' => $currency,
            ],
        ];
    }

    private function topCustomers(?int $assigneeId, int $limit = 10): array
    {
        $rows = $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_WON)
            ->whereNotNull('company_id')
            ->selectRaw('company_id, COUNT(*) as deals_count, COALESCE(SUM(value), 0) as total_value')
            ->groupBy('company_id')
            ->orderByDesc('total_value')
            ->limit($limit)
            ->get();

        $companies = Company::query()
            ->whereIn('id', $rows->pluck('company_id'))
            ->pluck('name', 'id');

        return $rows->map(fn ($row) => [
            'company_id' => (int) $row->company_id,
            'name' => $companies[$row->company_id] ?? __('crm::dashboard.unknown_customer'),
            'deals_count' => (int) $row->deals_count,
            'total_value' => (float) $row->total_value,
        ])->values()->all();
    }

    private function pipelineFunnel(?int $assigneeId): array
    {
        $stages = PipelineStage::query()->active()->ordered()->get();
        $maxCount = 1;

        $unconvertedLeads = $this->leadsQuery($assigneeId)->whereNull('deal_id')->count();
        $maxCount = max($maxCount, $unconvertedLeads);

        $aggregates = $this->dealsQuery($assigneeId)
            ->selectRaw('pipeline_stage_id, COUNT(*) as count, COALESCE(SUM(value), 0) as value')
            ->groupBy('pipeline_stage_id')
            ->get()
            ->keyBy('pipeline_stage_id');

        $stageStats = $stages->map(function (PipelineStage $stage) use ($aggregates, &$maxCount) {
            $row = $aggregates->get($stage->id);
            $count = (int) ($row->count ?? 0);
            $maxCount = max($maxCount, $count);

            return [
                'id' => $stage->id,
                'name' => $stage->name,
                'color' => $stage->color,
                'count' => $count,
                'value' => (float) ($row->value ?? 0),
                'is_won' => $stage->is_won,
                'is_lost' => $stage->is_lost,
            ];
        });

        $funnel = collect([
            [
                'id' => 'leads',
                'name' => __('crm::dashboard.funnel.unconverted_leads'),
                'color' => 'info',
                'count' => $unconvertedLeads,
                'value' => 0,
                'is_won' => false,
                'is_lost' => false,
            ],
        ])->merge($stageStats);

        return $funnel->map(function (array $row) use ($maxCount) {
            $row['percentage'] = $maxCount > 0 ? round(($row['count'] / $maxCount) * 100) : 0;

            return $row;
        })->values()->all();
    }

    private function openLeadsByStages(?int $assigneeId): array
    {
        $counts = $this->leadsQuery($assigneeId)
            ->whereIn('status', self::IN_PROGRESS_LEAD_STATUSES)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $colors = ['#B8EBEB', '#8FD9D9', '#66C7C7'];

        return collect(self::IN_PROGRESS_LEAD_STATUSES)
            ->values()
            ->map(function (string $status, int $index) use ($counts, $colors) {
                return [
                    'status' => $status,
                    'name' => __('crm::lead.status.'.$status),
                    'count' => (int) ($counts[$status] ?? 0),
                    'color' => $colors[$index] ?? $colors[array_key_last($colors)],
                ];
            })
            ->all();
    }

    private function leadChannels(array $range, ?int $assigneeId): array
    {
        $rows = $this->leadsQuery($assigneeId)
            ->select('source', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->groupBy('source')
            ->orderByDesc('total')
            ->get();

        $total = max(1, $rows->sum('total'));

        $channelMap = collect(Lead::SOURCES)->mapWithKeys(fn (string $source) => [
            $source => __('crm::dashboard.channels.'.$source),
        ])->all();

        return $rows->map(fn ($row) => [
            'key' => $row->source ?: 'unknown',
            'label' => $channelMap[$row->source] ?? __('crm::dashboard.channels.unknown'),
            'count' => (int) $row->total,
            'percentage' => round(((int) $row->total / $total) * 100, 1),
        ])->values()->all();
    }

    private function teamLeaderboard(array $range, ?int $assigneeId): array
    {
        $rows = $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_WON)
            ->whereBetween('won_at', [$range['start'], $range['end']])
            ->whereNotNull('assigned_to')
            ->selectRaw('assigned_to, COUNT(*) as deals_count, COALESCE(SUM(value), 0) as total_value')
            ->groupBy('assigned_to')
            ->orderByDesc('deals_count')
            ->limit(10)
            ->get();

        $employeeIds = $rows->pluck('assigned_to')->map(fn ($id) => (int) $id)->all();
        $targets = app(SalesTargetService::class)->targetsForEmployees($employeeIds);

        $employees = Employee::query()
            ->whereIn('id', $employeeIds)
            ->pluck('name', 'id');

        return $rows->map(function ($row) use ($employees, $targets) {
            $count = (int) $row->deals_count;
            $target = $targets[(int) $row->assigned_to] ?? (int) config('crm.sales_target_per_period', 10);
            $achievement = $target > 0 ? min(100, round(($count / $target) * 100)) : 0;

            return [
                'employee_id' => (int) $row->assigned_to,
                'name' => $employees[$row->assigned_to] ?? __('crm::dashboard.unknown_rep'),
                'closed_deals' => $count,
                'closed_value' => (float) $row->total_value,
                'target' => $target,
                'achievement' => $achievement,
            ];
        })->values()->all();
    }

    private function recentActivity(?int $assigneeId): array
    {
        $auditQuery = CrmAuditLog::query()
            ->with('user:id,name')
            ->latest('created_at')
            ->limit(20);

        $activityQuery = CrmActivity::query()
            ->with('user:id,name')
            ->latest()
            ->limit(20);

        if ($assigneeId) {
            $dealIds = $this->dealsQuery($assigneeId)->select('id');

            $auditQuery->where(function ($q) use ($dealIds) {
                $q->where('subject_type', Deal::class)->whereIn('subject_id', $dealIds);
            });

            $activityQuery->where(function ($q) use ($dealIds) {
                $q->where('subject_type', Deal::class)->whereIn('subject_id', $dealIds);
            });
        }

        $audits = $auditQuery->get()->map(function (CrmAuditLog $log) {
            $userName = $log->user?->name ?? __('crm::timeline.system');

            return [
                'kind' => 'audit',
                'message' => $log->description ?? __('crm::dashboard.activity.fallback'),
                'user' => $userName,
                'initials' => $this->initials($userName),
                'occurred_at' => $log->created_at,
                'event' => $log->event,
                'type_label' => __('crm::timeline.events.'.$log->event),
                'icon' => $this->eventIcon($log->event),
                'color' => $this->eventColor($log->event),
                'url' => $this->subjectUrl($log->subject_type, (int) $log->subject_id),
                'excerpt' => null,
            ];
        });

        $activities = $activityQuery->get()->map(function (CrmActivity $activity) {
            $userName = $activity->user?->name ?? __('crm::timeline.system');
            $title = $activity->title ?: Str::limit((string) $activity->body, 60);

            return [
                'kind' => 'activity',
                'message' => __('crm::dashboard.activity.logged', [
                    'type' => __('crm::timeline.activity_types.'.$activity->type),
                    'title' => $title,
                ]),
                'user' => $userName,
                'initials' => $this->initials($userName),
                'occurred_at' => $activity->created_at,
                'event' => $activity->type,
                'type_label' => __('crm::timeline.activity_types.'.$activity->type),
                'icon' => $this->activityIcon($activity->type),
                'color' => $this->activityColor($activity->type),
                'url' => $this->subjectUrl($activity->subject_type, (int) $activity->subject_id),
                'excerpt' => $activity->body ? Str::limit(strip_tags($activity->body), 110) : null,
            ];
        });

        return $audits->merge($activities)
            ->sortByDesc('occurred_at')
            ->take(15)
            ->values()
            ->all();
    }

    private function assignees(): Collection
    {
        return Employee::query()
            ->assignable()
            ->select(['id', 'name'])
            ->get();
    }

    private function leadsQuery(?int $assigneeId)
    {
        $query = Lead::query();

        if ($assigneeId) {
            $query->where('assigned_to', $assigneeId);
        }

        return $query;
    }

    private function dealsQuery(?int $assigneeId)
    {
        $query = Deal::query()->visibleTo();

        if ($assigneeId) {
            $query->where('assigned_to', $assigneeId);
        }

        return $query;
    }

    private function trend(float|int $current, float|int $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function eventIcon(string $event): string
    {
        return match ($event) {
            'created' => 'plus-circle',
            'deleted', 'activity_removed' => 'trash',
            'stage_changed' => 'arrow-right-circle',
            'converted' => 'arrow-repeat',
            'updated' => 'pencil-square',
            default => 'clock-history',
        };
    }

    private function eventColor(string $event): string
    {
        return match ($event) {
            'created', 'converted' => 'success',
            'deleted', 'activity_removed' => 'danger',
            'stage_changed' => 'info',
            default => 'secondary',
        };
    }

    private function activityIcon(string $type): string
    {
        return match ($type) {
            CrmActivity::TYPE_CALL => 'telephone',
            CrmActivity::TYPE_MEETING => 'people',
            CrmActivity::TYPE_TASK => 'check2-square',
            CrmActivity::TYPE_EMAIL => 'envelope',
            default => 'journal-text',
        };
    }

    private function activityColor(string $type): string
    {
        return match ($type) {
            CrmActivity::TYPE_CALL => 'success',
            CrmActivity::TYPE_MEETING => 'warning',
            CrmActivity::TYPE_TASK => 'primary',
            CrmActivity::TYPE_EMAIL => 'info',
            CrmActivity::TYPE_NOTE => 'secondary',
            default => 'primary',
        };
    }

    /**
     * @param  array<string, array<string, mixed>>  $summary
     * @return array<string, array<string, mixed>>
     */
    private function attachSparklines(array $summary, ?int $assigneeId): array
    {
        $map = [
            'new_customers' => ['query' => Company::query(), 'column' => 'created_at'],
            'total_customers' => ['query' => Company::query(), 'column' => 'created_at'],
            'new_leads' => ['query' => $this->leadsQuery($assigneeId), 'column' => 'created_at'],
            'total_leads' => ['query' => $this->leadsQuery($assigneeId), 'column' => 'created_at'],
            'won_deals' => [
                'query' => $this->dealsQuery($assigneeId)->where('status', Deal::STATUS_WON),
                'column' => 'won_at',
            ],
            'lost_deals' => [
                'query' => $this->dealsQuery($assigneeId)->where('status', Deal::STATUS_LOST),
                'column' => 'lost_at',
            ],
            'sales_this_month' => [
                'query' => $this->dealsQuery($assigneeId)->where('status', Deal::STATUS_WON),
                'column' => 'won_at',
                'sum' => 'value',
            ],
            'pipeline_value' => [
                'query' => $this->dealsQuery($assigneeId)->where('status', Deal::STATUS_OPEN),
                'column' => 'created_at',
                'sum' => 'value',
            ],
        ];

        foreach ($map as $key => $config) {
            if (! isset($summary[$key])) {
                continue;
            }

            $series = $this->lastDaysSeries(
                $config['query'],
                $config['column'],
                7,
                isset($config['sum']),
                $config['sum'] ?? 'value',
            );

            $summary[$key]['sparkline'] = $series;
            $peak = max($series) ?: 1;
            $summary[$key]['progress'] = $summary[$key]['progress']
                ?? (int) round((end($series) / $peak) * 100);
        }

        return $summary;
    }

    /**
     * @return list<float>
     */
    private function lastDaysSeries($query, string $column, int $days = 7, bool $sum = false, string $sumColumn = 'value'): array
    {
        $start = Carbon::now()->subDays($days - 1)->startOfDay();
        $end = Carbon::now()->endOfDay();
        $dateExpr = $this->dateExpression($column);
        $valueExpr = $sum
            ? "COALESCE(SUM({$sumColumn}), 0)"
            : 'COUNT(*)';

        $rows = (clone $query)
            ->whereNotNull($column)
            ->whereBetween($column, [$start, $end])
            ->selectRaw("{$dateExpr} as d, {$valueExpr} as v")
            ->groupByRaw($dateExpr)
            ->pluck('v', 'd');

        $series = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();
            $series[] = (float) ($rows[$date] ?? $rows[$date.' 00:00:00'] ?? 0);
        }

        return $series;
    }

    private function dateExpression(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "date({$column})",
            'pgsql' => "({$column})::date",
            default => "DATE({$column})",
        };
    }

    private function subjectUrl(?string $subjectType, int $subjectId): ?string
    {
        if (! $subjectType || $subjectId < 1) {
            return null;
        }

        $map = array_flip(CrmSubjectResolver::MAP);
        $key = $map[$subjectType] ?? (isset(CrmSubjectResolver::MAP[$subjectType]) ? $subjectType : null);

        return match ($key) {
            'company' => route('admin.companies.show', $subjectId),
            'contact' => route('admin.contacts.show', $subjectId),
            'deal' => route('admin.deals.show', $subjectId),
            'subscription' => route('admin.subscriptions.show', $subjectId),
            'lead' => route('admin.leads.show', $subjectId),
            default => null,
        };
    }

    private function initials(?string $name): string
    {
        $name = trim((string) $name);
        if ($name === '') {
            return '?';
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        if (count($parts) >= 2) {
            return mb_strtoupper(mb_substr($parts[0], 0, 1).mb_substr($parts[1], 0, 1));
        }

        return mb_strtoupper(mb_substr($name, 0, 2));
    }
}

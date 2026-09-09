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
use Modules\CRM\Support\CrmSubjectResolver;
use Modules\CRM\Support\DateRangeResolver;
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
            'lead_channels' => $leadChannels,
            'team_leaderboard' => $teamLeaderboard,
            'recent_activity' => $recentActivity,
            'top_customers' => $topCustomers,
            'widgets' => $this->widgetDataMap($summary, $pipelineFunnel, $leadChannels, $teamLeaderboard, $recentActivity, $topCustomers),
            'assignees' => $this->assignees(),
            'chart_colors' => [
                '#3E97FF', '#50CD89', '#FFC700', '#7239EA', '#F1416C', '#181C32', '#A1A5B7',
            ],
            'currency' => $currency,
        ];
    }

    private function widgetDataMap(
        array $summary,
        array $pipelineFunnel,
        array $leadChannels,
        array $teamLeaderboard,
        array $recentActivity,
        array $topCustomers,
    ): array {
        return array_merge($summary, [
            'pipeline_funnel' => $pipelineFunnel,
            'lead_channels' => $leadChannels,
            'sales_performance' => $teamLeaderboard,
            'recent_activity' => $recentActivity,
            'top_customers' => $topCustomers,
        ]);
    }

    private function summaryMetrics(array $range, ?int $assigneeId, string $currency): array
    {
        $totalCustomers = Company::query()->count();
        $previousCustomers = Company::query()
            ->where('created_at', '<=', $range['previous_end'])
            ->count();

        $newCustomers = Company::query()
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->count();
        $previousNewCustomers = Company::query()
            ->whereBetween('created_at', [$range['previous_start'], $range['previous_end']])
            ->count();

        $activeCustomers = Company::query()->where('status', Company::STATUS_ACTIVE)->count();
        $lostCustomers = Company::query()->where('status', Company::STATUS_DISABLED)->count();

        $currentLeads = $this->leadsQuery($assigneeId)
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->count();
        $previousLeads = $this->leadsQuery($assigneeId)
            ->whereBetween('created_at', [$range['previous_start'], $range['previous_end']])
            ->count();

        $leadsInProgress = $this->leadsQuery($assigneeId)
            ->whereIn('status', self::IN_PROGRESS_LEAD_STATUSES)
            ->count();

        $totalLeadsAll = $this->leadsQuery($assigneeId)->count();
        $convertedToWon = $this->leadsQuery($assigneeId)
            ->whereNotNull('deal_id')
            ->whereHas('deal', fn ($q) => $q->where('status', Deal::STATUS_WON))
            ->count();

        $conversionRate = $totalLeadsAll > 0
            ? round(($convertedToWon / $totalLeadsAll) * 100, 1)
            : 0;

        $previousConverted = $this->leadsQuery($assigneeId)
            ->whereNotNull('deal_id')
            ->whereHas('deal', fn ($q) => $q
                ->where('status', Deal::STATUS_WON)
                ->whereBetween('won_at', [$range['previous_start'], $range['previous_end']]))
            ->count();

        $currentConverted = $this->leadsQuery($assigneeId)
            ->whereNotNull('deal_id')
            ->whereHas('deal', fn ($q) => $q
                ->where('status', Deal::STATUS_WON)
                ->whereBetween('won_at', [$range['start'], $range['end']]))
            ->count();

        $pipelineValue = (float) $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_OPEN)
            ->sum('value');

        $previousPipelineValue = (float) $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_OPEN)
            ->where('created_at', '<=', $range['previous_end'])
            ->sum('value');

        $wonDealsQuery = $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_WON)
            ->whereBetween('won_at', [$range['start'], $range['end']]);

        $wonDealsCount = (clone $wonDealsQuery)->count();
        $wonDealsValue = (float) (clone $wonDealsQuery)->sum('value');

        $previousWonCount = $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_WON)
            ->whereBetween('won_at', [$range['previous_start'], $range['previous_end']])
            ->count();

        $lostDealsQuery = $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_LOST)
            ->whereBetween('lost_at', [$range['start'], $range['end']]);

        $lostDealsCount = (clone $lostDealsQuery)->count();
        $lostDealsValue = (float) (clone $lostDealsQuery)->sum('value');

        $previousLostCount = $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_LOST)
            ->whereBetween('lost_at', [$range['previous_start'], $range['previous_end']])
            ->count();

        $totalSales = (float) $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_WON)
            ->sum('value');

        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $salesThisMonth = (float) $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_WON)
            ->whereBetween('won_at', [$monthStart, $monthEnd])
            ->sum('value');

        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $previousSalesThisMonth = (float) $this->dealsQuery($assigneeId)
            ->where('status', Deal::STATUS_WON)
            ->whereBetween('won_at', [$previousMonthStart, $previousMonthEnd])
            ->sum('value');

        $avgDealValue = $wonDealsCount > 0
            ? round($wonDealsValue / $wonDealsCount, 2)
            : 0.0;

        $avgCloseTime = $this->averageCloseDays($range, $assigneeId);

        $invoiceMetrics = $this->invoiceMetrics($currency);

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
                'value' => $currentLeads,
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

    private function invoiceMetrics(string $currency): array
    {
        $empty = [
            'outstanding' => [
                'count' => 0,
                'value' => 0.0,
                'trend' => 0,
                'currency' => $currency,
            ],
            'overdue' => [
                'value' => 0.0,
                'count' => 0,
                'trend' => 0,
                'currency' => $currency,
            ],
        ];

        if (! class_exists(\Modules\Finance\Models\Invoice::class)) {
            return $empty;
        }

        $invoiceClass = \Modules\Finance\Models\Invoice::class;

        $outstandingQuery = $invoiceClass::query()->open();
        $outstandingCount = (clone $outstandingQuery)->count();
        $outstandingValue = (float) (clone $outstandingQuery)->sum('total');

        $overdueQuery = $invoiceClass::query()->where('status', $invoiceClass::STATUS_OVERDUE);
        $overdueCount = (clone $overdueQuery)->count();
        $overdueValue = (float) (clone $overdueQuery)->sum('total');

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

        $stageStats = $stages->map(function (PipelineStage $stage) use ($assigneeId, &$maxCount) {
            $query = $this->dealsQuery($assigneeId)->where('pipeline_stage_id', $stage->id);
            $count = (clone $query)->count();
            $value = (float) (clone $query)->sum('value');
            $maxCount = max($maxCount, $count);

            return [
                'id' => $stage->id,
                'name' => $stage->name,
                'color' => $stage->color,
                'count' => $count,
                'value' => $value,
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
        $targets = app(\Modules\CRM\Services\SalesTarget\SalesTargetService::class)->targetsForEmployees($employeeIds);

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
        $dealIds = $assigneeId
            ? $this->dealsQuery($assigneeId)->pluck('id')
            : null;

        $auditQuery = CrmAuditLog::query()
            ->with('user:id,name')
            ->latest('created_at')
            ->limit(20);

        $activityQuery = CrmActivity::query()
            ->with('user:id,name')
            ->latest()
            ->limit(20);

        if ($assigneeId && $dealIds && $dealIds->isNotEmpty()) {
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
            'call' => 'telephone',
            'meeting' => 'people',
            'task' => 'check2-square',
            'email' => 'envelope',
            default => 'journal-text',
        };
    }

    private function activityColor(string $type): string
    {
        return match ($type) {
            'call' => 'success',
            'meeting' => 'warning',
            'task' => 'primary',
            'email' => 'info',
            'note' => 'secondary',
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

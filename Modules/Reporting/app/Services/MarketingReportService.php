<?php

namespace Modules\Reporting\Services;

use Illuminate\Support\Facades\DB;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\MarketingCampaign;
use Modules\CRM\Models\WhatsAppCampaign;
use Modules\CRM\Models\WhatsAppMessageLog;
use Modules\Reporting\DTOs\ReportFilters;

class MarketingReportService extends BaseReportService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $resolved = $this->resolveFilters($filters);

        $emailCampaigns = $this->emailCampaignStats($resolved);
        $whatsappCampaigns = $this->whatsappCampaignStats($resolved);
        $leadSources = $this->leadSourceBreakdown($resolved);
        $conversionBySource = $this->conversionBySource($resolved);

        $totalRecipients = $emailCampaigns['total_recipients'] + $whatsappCampaigns['total_recipients'];
        $prevEmail = $this->emailCampaignStats($resolved, previous: true);
        $prevWhatsapp = $this->whatsappCampaignStats($resolved, previous: true);
        $prevRecipients = $prevEmail['total_recipients'] + $prevWhatsapp['total_recipients'];

        return [
            'filters' => $resolved->toArray(),
            'chart_colors' => $this->chartColors(),
            'kpis' => [
                'email_campaigns' => $this->kpiMetric(
                    (float) $emailCampaigns['finished_count'],
                    (float) $prevEmail['finished_count'],
                ),
                'whatsapp_campaigns' => $this->kpiMetric(
                    (float) $whatsappCampaigns['finished_count'],
                    (float) $prevWhatsapp['finished_count'],
                ),
                'total_recipients' => $this->kpiMetric((float) $totalRecipients, (float) $prevRecipients),
                'whatsapp_delivery_rate' => [
                    'value' => $whatsappCampaigns['delivery_rate'],
                    'previous' => $prevWhatsapp['delivery_rate'],
                    'change' => $this->percentChange(
                        $whatsappCampaigns['delivery_rate'],
                        $prevWhatsapp['delivery_rate'],
                    ),
                    'trend' => $this->trendDirection(
                        $whatsappCampaigns['delivery_rate'],
                        $prevWhatsapp['delivery_rate'],
                    ),
                ],
                'new_leads' => $this->kpiMetric(
                    (float) Lead::query()->whereBetween('created_at', [$resolved->start, $resolved->end])->count(),
                    (float) Lead::query()->whereBetween('created_at', [$resolved->previousStart, $resolved->previousEnd])->count(),
                ),
            ],
            'charts' => [
                'campaign_volume' => $this->campaignVolumeTrend($resolved),
                'lead_sources' => $leadSources,
                'whatsapp_delivery' => [
                    'sent' => $whatsappCampaigns['sent_count'],
                    'failed' => $whatsappCampaigns['failed_count'],
                    'pending' => $whatsappCampaigns['pending_count'],
                ],
                'conversion_by_source' => $conversionBySource,
            ],
            'tables' => [
                'email_campaigns' => $this->recentEmailCampaigns($resolved),
                'whatsapp_campaigns' => $this->recentWhatsappCampaigns($resolved),
                'lead_sources' => $leadSources,
            ],
        ];
    }

    /**
     * @return array{finished_count: int, total_recipients: int, pending_count: int, failed_count: int}
     */
    private function emailCampaignStats(ReportFilters $filters, bool $previous = false): array
    {
        $start = $previous ? $filters->previousStart : $filters->start;
        $end = $previous ? $filters->previousEnd : $filters->end;

        $rows = MarketingCampaign::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('status, COUNT(*) as count, COALESCE(SUM(recipients_count), 0) as recipients')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return [
            'finished_count' => (int) ($rows[MarketingCampaign::STATUS_FINISHED]->count ?? 0),
            'total_recipients' => (int) ($rows[MarketingCampaign::STATUS_FINISHED]->recipients ?? 0),
            'pending_count' => (int) ($rows[MarketingCampaign::STATUS_PENDING]->count ?? 0),
            'failed_count' => (int) ($rows[MarketingCampaign::STATUS_FAILED]->count ?? 0),
        ];
    }

    /**
     * @return array{finished_count: int, total_recipients: int, sent_count: int, failed_count: int, pending_count: int, delivery_rate: float}
     */
    private function whatsappCampaignStats(ReportFilters $filters, bool $previous = false): array
    {
        $start = $previous ? $filters->previousStart : $filters->start;
        $end = $previous ? $filters->previousEnd : $filters->end;

        $campaigns = WhatsAppCampaign::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('status, COUNT(*) as count, COALESCE(SUM(recipients_count), 0) as recipients')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $logs = WhatsAppMessageLog::query()
            ->whereHas('campaign', fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $sent = (int) ($logs[WhatsAppMessageLog::STATUS_SENT] ?? 0);
        $failed = (int) ($logs[WhatsAppMessageLog::STATUS_FAILED] ?? 0);
        $pending = (int) ($logs[WhatsAppMessageLog::STATUS_PENDING] ?? 0);
        $total = $sent + $failed + $pending;
        $finished = $campaigns[WhatsAppCampaign::STATUS_FINISHED] ?? null;

        return [
            'finished_count' => (int) ($finished->count ?? 0),
            'total_recipients' => (int) ($finished->recipients ?? 0),
            'sent_count' => $sent,
            'failed_count' => $failed,
            'pending_count' => $pending,
            'delivery_rate' => $total > 0 ? round(($sent / $total) * 100, 1) : 0.0,
        ];
    }

    /**
     * @return array<int, array{source: string, count: int, label: string}>
     */
    private function leadSourceBreakdown(ReportFilters $filters): array
    {
        return Lead::query()
            ->select(['source', DB::raw('COUNT(*) as count')])
            ->whereBetween('created_at', [$filters->start, $filters->end])
            ->groupBy('source')
            ->orderByDesc('count')
            ->get()
            ->map(fn ($row) => [
                'source' => $row->source,
                'label' => __('crm::lead.sources.'.$row->source, [], $row->source),
                'count' => (int) $row->count,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{source: string, label: string, leads: int, conversions: int, rate: float}>
     */
    private function conversionBySource(ReportFilters $filters): array
    {
        return Lead::query()
            ->leftJoin('deals', function ($join) {
                $join->on('deals.id', '=', 'leads.deal_id')
                    ->where('deals.status', Deal::STATUS_WON)
                    ->whereNull('deals.deleted_at');
            })
            ->whereBetween('leads.created_at', [$filters->start, $filters->end])
            ->selectRaw('leads.source, COUNT(*) as leads, COUNT(deals.id) as conversions')
            ->groupBy('leads.source')
            ->get()
            ->map(fn ($row) => [
                'source' => $row->source,
                'label' => __('crm::lead.sources.'.$row->source, [], $row->source),
                'leads' => (int) $row->leads,
                'conversions' => (int) $row->conversions,
                'rate' => $row->leads > 0 ? round(($row->conversions / $row->leads) * 100, 1) : 0.0,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label: string, email: int, whatsapp: int}>
     */
    private function campaignVolumeTrend(ReportFilters $filters): array
    {
        $email = MarketingCampaign::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as count")
            ->whereBetween('created_at', [$filters->start, $filters->end])
            ->groupBy('period')
            ->pluck('count', 'period');

        $whatsapp = WhatsAppCampaign::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as count")
            ->whereBetween('created_at', [$filters->start, $filters->end])
            ->groupBy('period')
            ->pluck('count', 'period');

        $periods = $email->keys()->merge($whatsapp->keys())->unique()->sort()->values();

        return $periods->map(fn (string $period) => [
            'label' => $period,
            'email' => (int) ($email[$period] ?? 0),
            'whatsapp' => (int) ($whatsapp[$period] ?? 0),
        ])->values()->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function recentEmailCampaigns(ReportFilters $filters): array
    {
        return MarketingCampaign::query()
            ->with('user:id,name')
            ->whereBetween('created_at', [$filters->start, $filters->end])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn (MarketingCampaign $c) => [
                'subject' => $c->subject,
                'recipients' => $c->recipients_count,
                'status' => $c->status,
                'sender' => $c->user?->name,
                'date' => $c->created_at->toDateString(),
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function recentWhatsappCampaigns(ReportFilters $filters): array
    {
        return WhatsAppCampaign::query()
            ->with(['user:id,name', 'template:id,name'])
            ->whereBetween('created_at', [$filters->start, $filters->end])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn (WhatsAppCampaign $c) => [
                'template' => $c->template?->name,
                'recipients' => $c->recipients_count,
                'status' => $c->status,
                'sender' => $c->user?->name,
                'date' => $c->created_at->toDateString(),
            ])
            ->all();
    }
}

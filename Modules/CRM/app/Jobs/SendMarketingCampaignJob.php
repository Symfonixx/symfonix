<?php

namespace Modules\CRM\Jobs;

use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Modules\CRM\Models\MarketingCampaign;
use Modules\CRM\Models\MarketingEmailLog;
use Throwable;

class SendMarketingCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 300;

    public function __construct(
        public int $campaignId,
        public string $locale = 'en',
    ) {}

    public function handle(): void
    {
        $campaign = MarketingCampaign::query()->findOrFail($this->campaignId);

        $pendingLogIds = MarketingEmailLog::query()
            ->where('marketing_campaign_id', $campaign->id)
            ->where('status', MarketingEmailLog::STATUS_PENDING)
            ->orderBy('id')
            ->pluck('id');

        if ($pendingLogIds->isEmpty()) {
            $campaign->markAsFinished();

            return;
        }

        $campaign->markAsSending();

        $jobs = $pendingLogIds
            ->chunk(SendMarketingEmailChunkJob::CHUNK_SIZE)
            ->map(fn ($chunk) => new SendMarketingEmailChunkJob(
                $this->campaignId,
                $chunk->values()->all(),
                $this->locale,
            ))
            ->values()
            ->all();

        $campaignId = $this->campaignId;

        Bus::batch($jobs)
            ->name("marketing-email-{$campaignId}")
            ->allowFailures()
            ->finally(function (Batch $batch) use ($campaignId) {
                self::finalizeCampaign($campaignId);
            })
            ->dispatch();
    }

    public function failed(Throwable $exception): void
    {
        report($exception);

        MarketingCampaign::query()
            ->whereKey($this->campaignId)
            ->update(['status' => MarketingCampaign::STATUS_FAILED]);
    }

    public static function finalizeCampaign(int $campaignId): void
    {
        $campaign = MarketingCampaign::query()->find($campaignId);

        if (! $campaign) {
            return;
        }

        $queuedCount = MarketingEmailLog::query()
            ->where('marketing_campaign_id', $campaignId)
            ->where('status', MarketingEmailLog::STATUS_QUEUED)
            ->count();

        $failedCount = MarketingEmailLog::query()
            ->where('marketing_campaign_id', $campaignId)
            ->where('status', MarketingEmailLog::STATUS_FAILED)
            ->count();

        if ($queuedCount === 0 && $failedCount > 0) {
            $campaign->markAsFailed();

            return;
        }

        $campaign->markAsFinished();
    }
}

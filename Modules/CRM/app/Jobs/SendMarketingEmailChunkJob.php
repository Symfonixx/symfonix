<?php

namespace Modules\CRM\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\CRM\Mail\MarketingEmail;
use Modules\CRM\Models\MarketingCampaign;
use Modules\CRM\Models\MarketingEmailLog;
use Throwable;

class SendMarketingEmailChunkJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    /**
     * Number of recipient emails processed per chunk job.
     */
    public const CHUNK_SIZE = 50;

    /**
     * @param  array<int, int>  $logIds
     */
    public function __construct(
        public int $campaignId,
        public array $logIds,
        public string $locale = 'en',
    ) {}

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $campaign = MarketingCampaign::query()->find($this->campaignId);

        if (! $campaign) {
            return;
        }

        $logs = MarketingEmailLog::query()
            ->where('marketing_campaign_id', $this->campaignId)
            ->whereIn('id', $this->logIds)
            ->where('status', MarketingEmailLog::STATUS_PENDING)
            ->get();

        foreach ($logs as $log) {
            try {
                Mail::to($log->email)->queue(new MarketingEmail(
                    $campaign->subject,
                    $campaign->body,
                    $this->locale,
                ));

                $log->markAsQueued();
            } catch (Throwable $exception) {
                report($exception);
                $log->markAsFailed($exception->getMessage());
            }
        }
    }
}

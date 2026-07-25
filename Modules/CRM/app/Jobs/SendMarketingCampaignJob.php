<?php

namespace Modules\CRM\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\CRM\Mail\MarketingEmail;
use Modules\CRM\Models\MarketingCampaign;
use Throwable;

class SendMarketingCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 300;

    /**
     * @param  array<int, string>  $recipients
     */
    public function __construct(
        public int $campaignId,
        public array $recipients,
        public string $locale = 'en',
    ) {}

    public function handle(): void
    {
        $campaign = MarketingCampaign::query()->findOrFail($this->campaignId);

        foreach ($this->recipients as $email) {
            Mail::to($email)->queue(new MarketingEmail(
                $campaign->subject,
                $campaign->body,
                $this->locale,
            ));
        }

        $campaign->markAsFinished();
    }

    public function failed(Throwable $exception): void
    {
        report($exception);

        MarketingCampaign::query()
            ->whereKey($this->campaignId)
            ->update(['status' => MarketingCampaign::STATUS_FAILED]);
    }
}

<?php

namespace Tests\Feature\Marketing;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Modules\CRM\Jobs\SendMarketingCampaignJob;
use Modules\CRM\Jobs\SendMarketingEmailChunkJob;
use Modules\CRM\Mail\MarketingEmail;
use Modules\CRM\Models\MarketingCampaign;
use Modules\CRM\Models\MarketingEmailLog;
use Tests\TestCase;

class MarketingEmailChunkQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_campaign_job_batches_recipient_chunks(): void
    {
        Bus::fake();

        $campaign = MarketingCampaign::query()->create([
            'user_id' => User::factory()->create()->id,
            'subject' => 'Hello',
            'body' => '<p>World</p>',
            'recipients_count' => 3,
            'status' => MarketingCampaign::STATUS_PENDING,
        ]);

        $now = now();
        MarketingEmailLog::query()->insert([
            [
                'marketing_campaign_id' => $campaign->id,
                'email' => 'a@example.com',
                'status' => MarketingEmailLog::STATUS_PENDING,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'marketing_campaign_id' => $campaign->id,
                'email' => 'b@example.com',
                'status' => MarketingEmailLog::STATUS_PENDING,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'marketing_campaign_id' => $campaign->id,
                'email' => 'c@example.com',
                'status' => MarketingEmailLog::STATUS_PENDING,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        (new SendMarketingCampaignJob($campaign->id, 'en'))->handle();

        $campaign->refresh();
        $this->assertSame(MarketingCampaign::STATUS_SENDING, $campaign->status);

        Bus::assertBatched(function ($batch) use ($campaign) {
            return $batch->name === "marketing-email-{$campaign->id}"
                && $batch->jobs->count() === 1
                && $batch->jobs->first() instanceof SendMarketingEmailChunkJob;
        });
    }

    public function test_chunk_job_queues_mailables_and_marks_logs(): void
    {
        Mail::fake();

        $campaign = MarketingCampaign::query()->create([
            'user_id' => User::factory()->create()->id,
            'subject' => 'Hello',
            'body' => '<p>World</p>',
            'recipients_count' => 2,
            'status' => MarketingCampaign::STATUS_SENDING,
        ]);

        $logs = collect(['one@example.com', 'two@example.com'])->map(fn (string $email) => MarketingEmailLog::query()->create([
            'marketing_campaign_id' => $campaign->id,
            'email' => $email,
            'status' => MarketingEmailLog::STATUS_PENDING,
        ]));

        (new SendMarketingEmailChunkJob(
            $campaign->id,
            $logs->pluck('id')->all(),
            'en',
        ))->handle();

        Mail::assertQueued(MarketingEmail::class, 2);

        $this->assertSame(2, MarketingEmailLog::query()
            ->where('marketing_campaign_id', $campaign->id)
            ->where('status', MarketingEmailLog::STATUS_QUEUED)
            ->count());
    }

    public function test_finalize_marks_finished_when_any_queued(): void
    {
        $campaign = MarketingCampaign::query()->create([
            'user_id' => User::factory()->create()->id,
            'subject' => 'Hello',
            'body' => '<p>World</p>',
            'recipients_count' => 2,
            'status' => MarketingCampaign::STATUS_SENDING,
        ]);

        MarketingEmailLog::query()->create([
            'marketing_campaign_id' => $campaign->id,
            'email' => 'ok@example.com',
            'status' => MarketingEmailLog::STATUS_QUEUED,
            'sent_at' => now(),
        ]);
        MarketingEmailLog::query()->create([
            'marketing_campaign_id' => $campaign->id,
            'email' => 'bad@example.com',
            'status' => MarketingEmailLog::STATUS_FAILED,
            'error_message' => 'boom',
        ]);

        SendMarketingCampaignJob::finalizeCampaign($campaign->id);

        $this->assertSame(MarketingCampaign::STATUS_FINISHED, $campaign->fresh()->status);
    }
}

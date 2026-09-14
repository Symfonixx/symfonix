<?php

namespace Tests\Feature\Marketing;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Modules\CRM\Jobs\SendWhatsAppCampaignJob;
use Modules\CRM\Models\WhatsAppCampaign;
use Modules\CRM\Models\WhatsAppMessageLog;
use Modules\CRM\Models\WhatsAppTemplate;
use Modules\CRM\Services\Marketing\WhatsAppCampaignService;
use Tests\TestCase;

class WhatsAppCampaignIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_campaign_creation_builds_logs_and_dispatches_job(): void
    {
        Queue::fake();
        putenv('WHATSAPP_API_TOKEN=test-token');
        putenv('WHATSAPP_PHONE_NUMBER_ID=15550001111');

        $admin = User::factory()->admin()->create();
        $template = WhatsAppTemplate::query()->create([
            'name' => 'promo_offer',
            'language' => 'en',
            'category' => 'MARKETING',
            'status' => WhatsAppTemplate::STATUS_APPROVED,
            'header_type' => WhatsAppTemplate::HEADER_NONE,
            'body' => 'Hello {{1}}, get {{2}}% off!',
            'is_active' => true,
            'user_id' => $admin->id,
        ]);

        $campaign = app(WhatsAppCampaignService::class)->send(
            $template->id,
            [1 => 'Hadi', 2 => '25'],
            ['custom_phones' => '+20100111222, +20100111222, +971501234567'],
            $admin->id
        );

        $campaign->refresh();

        $this->assertInstanceOf(WhatsAppCampaign::class, $campaign);
        $this->assertSame(2, $campaign->recipients_count);
        $this->assertDatabaseCount('whatsapp_message_logs', 2);
        $this->assertDatabaseHas('whatsapp_message_logs', [
            'whatsapp_campaign_id' => $campaign->id,
            'phone' => '+20100111222',
            'status' => WhatsAppMessageLog::STATUS_PENDING,
        ]);
        Queue::assertPushed(SendWhatsAppCampaignJob::class);
    }
}

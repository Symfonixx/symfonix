<?php

namespace Tests\Feature\Marketing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Modules\CRM\Jobs\SendMarketingCampaignJob;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\LeadTag;
use Modules\CRM\Models\MarketingCampaign;
use Modules\CRM\Models\MarketingGroup;
use Modules\CRM\Services\Marketing\WhatsAppCampaignService;
use Tests\Concerns\InteractsWithAdminPermissions;
use Tests\TestCase;

class MarketingLeadTagRecipientsTest extends TestCase
{
    use InteractsWithAdminPermissions;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            LocaleSessionRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ]);
    }

    public function test_email_compose_shows_lead_tags_to_check(): void
    {
        $user = $this->createAdminWithPermissions(['marketing.email.send']);
        $this->makeTags();

        $this->actingAs($user)
            ->get(route('admin.crm.marketing.create'))
            ->assertOk()
            ->assertSee(__('crm::marketing.fields.lead_tags'))
            ->assertSee('Cold')
            ->assertSee('Warm')
            ->assertSee('name="lead_tag_ids[]"', false);
    }

    public function test_email_send_includes_leads_from_checked_tags_once(): void
    {
        Queue::fake();
        $user = $this->createAdminWithPermissions(['marketing.email.send', 'marketing.email.view']);
        $group = MarketingGroup::query()->create([
            'user_id' => $user->id,
            'title' => 'Warm-up',
            'goal' => 'Re-engage cold and warm leads.',
        ]);

        [$cold, $warm] = $this->makeTags();
        $coldLead = $this->makeLead('Cold lead', 'cold@example.com', '+201001111111');
        $warmLead = $this->makeLead('Warm lead', 'warm@example.com', '+201002222222');
        $bothLead = $this->makeLead('Both tags', 'both@example.com', '+201003333333');
        $this->makeLead('Untagged', 'hot@example.com', '+201004444444');

        $coldLead->tags()->attach($cold->id);
        $warmLead->tags()->attach($warm->id);
        $bothLead->tags()->attach([$cold->id, $warm->id]);

        $this->actingAs($user)->post(route('admin.crm.marketing.store'), [
            'marketing_group_id' => $group->id,
            'subject' => 'Shall we talk?',
            'body' => '<p>Following up on your project.</p>',
            'lead_tag_ids' => [$cold->id, $warm->id],
        ])->assertRedirect(route('admin.crm.marketing.groups.show', $group));

        $campaign = MarketingCampaign::query()->first();

        $this->assertNotNull($campaign);
        $this->assertSame(3, $campaign->recipients_count);
        $this->assertEqualsCanonicalizing(
            [$cold->id, $warm->id],
            array_map('intval', $campaign->recipient_sources['lead_tag_ids'] ?? []),
        );
        Queue::assertPushed(SendMarketingCampaignJob::class);
    }

    public function test_whatsapp_resolve_includes_leads_from_checked_tags_once(): void
    {
        [$cold, $warm] = $this->makeTags();
        $coldLead = $this->makeLead('Cold lead', 'cold@example.com', '+201001111111');
        $warmLead = $this->makeLead('Warm lead', 'warm@example.com', '+201002222222');
        $bothLead = $this->makeLead('Both tags', 'both@example.com', '+201003333333');
        $this->makeLead('Untagged', 'hot@example.com', '+201004444444');

        $coldLead->tags()->attach($cold->id);
        $warmLead->tags()->attach($warm->id);
        $bothLead->tags()->attach([$cold->id, $warm->id]);

        $recipients = app(WhatsAppCampaignService::class)->resolveRecipients([
            'lead_tag_ids' => [$cold->id, $warm->id],
        ]);

        $this->assertCount(3, $recipients);
        $this->assertEqualsCanonicalizing(
            ['+201001111111', '+201002222222', '+201003333333'],
            $recipients->pluck('phone')->all(),
        );
    }

    /**
     * @return array{0: LeadTag, 1: LeadTag}
     */
    private function makeTags(): array
    {
        return [
            LeadTag::query()->create([
                'name' => ['en' => 'Cold'],
                'color' => 'info',
                'is_active' => true,
                'sort_order' => 1,
            ]),
            LeadTag::query()->create([
                'name' => ['en' => 'Warm'],
                'color' => 'warning',
                'is_active' => true,
                'sort_order' => 2,
            ]),
        ];
    }

    private function makeLead(string $name, string $email, string $phone): Lead
    {
        return Lead::query()->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'status' => Lead::STATUS_NEW,
        ]);
    }
}

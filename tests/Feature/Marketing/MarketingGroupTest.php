<?php

namespace Tests\Feature\Marketing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Modules\CRM\Jobs\SendMarketingCampaignJob;
use Modules\CRM\Models\MarketingCampaign;
use Modules\CRM\Models\MarketingGroup;
use Tests\Concerns\InteractsWithAdminPermissions;
use Tests\TestCase;

class MarketingGroupTest extends TestCase
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

    public function test_user_can_create_a_campaign_with_title_and_goal(): void
    {
        $user = $this->createAdminWithPermissions(['marketing.email.send', 'marketing.email.view']);

        $response = $this->actingAs($user)->post(route('admin.crm.marketing.groups.store'), [
            'title' => 'Spring website offer',
            'goal' => 'Book discovery calls for the new website package.',
        ]);

        $group = MarketingGroup::query()->first();

        $this->assertNotNull($group);
        $this->assertSame('Spring website offer', $group->title);
        $this->assertSame('Book discovery calls for the new website package.', $group->goal);
        $response->assertRedirect(route('admin.crm.marketing.groups.show', $group));
        $this->assertTrue($group->isIncomplete());
        $this->assertContains('sends', $group->missingKeys());
    }

    public function test_campaign_show_lists_missing_email_and_whatsapp_sends(): void
    {
        $user = $this->createAdminWithPermissions([
            'marketing.email.view',
            'marketing.whatsapp.view',
            'marketing.email.send',
        ]);
        $group = MarketingGroup::query()->create([
            'user_id' => $user->id,
            'title' => 'Spring website offer',
            'goal' => 'Book discovery calls.',
        ]);

        $this->actingAs($user)
            ->get(route('admin.crm.marketing.groups.show', $group))
            ->assertOk()
            ->assertSee(__('crm::marketing.missing.sends'))
            ->assertSee(__('crm::marketing.missing.heading'));
    }

    public function test_sending_an_email_attaches_it_to_the_campaign(): void
    {
        Queue::fake();
        $user = $this->createAdminWithPermissions(['marketing.email.send', 'marketing.email.view']);
        $group = MarketingGroup::query()->create([
            'user_id' => $user->id,
            'title' => 'Spring website offer',
            'goal' => 'Book discovery calls.',
        ]);

        $response = $this->actingAs($user)->post(route('admin.crm.marketing.store'), [
            'marketing_group_id' => $group->id,
            'subject' => 'Book a website call',
            'body' => '<p>Let us rebuild your site.</p>',
            'custom_emails' => 'prospect@example.com',
        ]);

        $campaign = MarketingCampaign::query()->first();

        $this->assertNotNull($campaign);
        $this->assertSame($group->id, $campaign->marketing_group_id);
        $response->assertRedirect(route('admin.crm.marketing.groups.show', $group));
        Queue::assertPushed(SendMarketingCampaignJob::class);

        $group->loadCount(['emailCampaigns', 'whatsappCampaigns']);
        $this->assertFalse($group->isIncomplete());
        $this->assertContains('whatsapp', $group->missingKeys());
        $this->assertNotContains('sends', $group->missingKeys());
    }

    public function test_email_send_requires_a_campaign_title_and_goal(): void
    {
        $user = $this->createAdminWithPermissions(['marketing.email.send']);

        $this->actingAs($user)
            ->from(route('admin.crm.marketing.create'))
            ->post(route('admin.crm.marketing.store'), [
                'subject' => 'Book a website call',
                'body' => '<p>Let us rebuild your site.</p>',
                'custom_emails' => 'prospect@example.com',
            ])
            ->assertRedirect(route('admin.crm.marketing.create'))
            ->assertSessionHasErrors(['group_title', 'group_goal']);
    }
}

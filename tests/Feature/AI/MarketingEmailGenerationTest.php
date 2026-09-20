<?php

namespace Tests\Feature\AI;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Mockery\MockInterface;
use Modules\AI\Services\GeminiTextService;
use Modules\AI\Services\OpenAITextService;
use Modules\CRM\Models\MarketingGroup;
use Tests\Concerns\InteractsWithAdminPermissions;
use Tests\TestCase;

class MarketingEmailGenerationTest extends TestCase
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

    public function test_user_without_permission_cannot_generate_marketing_email(): void
    {
        $user = $this->createAdminWithPermissions(['marketing.email.view']);

        $this->actingAs($user)
            ->postJson(route('admin.ai.content.generate-marketing-email'), [
                'title' => 'Spring website offer',
                'goal' => 'Book discovery calls.',
            ])
            ->assertForbidden();
    }

    public function test_it_requires_a_campaign_title_and_goal(): void
    {
        $user = $this->createAdminWithPermissions(['marketing.email.send']);

        $this->actingAs($user)
            ->postJson(route('admin.ai.content.generate-marketing-email'), [
                'prompt' => 'Mention Friday',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['goal']);
    }

    public function test_it_generates_subject_and_body_from_the_campaign_goal(): void
    {
        $user = $this->createAdminWithPermissions(['marketing.email.send']);
        $group = MarketingGroup::query()->create([
            'user_id' => $user->id,
            'title' => 'Spring website offer',
            'goal' => 'Book discovery calls for the new website package.',
        ]);

        $this->mock(OpenAITextService::class, function (MockInterface $mock) use ($group): void {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('generateStructured')
                ->once()
                ->withArgs(function (string $systemPrompt, string $userMessage) use ($group) {
                    return str_contains($systemPrompt, 'marketing email')
                        && str_contains($userMessage, $group->title)
                        && str_contains($userMessage, $group->goal)
                        && str_contains($userMessage, 'Mention Friday');
                })
                ->andReturn([
                    'success' => true,
                    'content' => json_encode([
                        'subject' => 'Book your spring website call',
                        'body' => '<p>Let us rebuild your site this spring.</p><p>Reply to book a Friday call.</p>',
                    ]),
                    'error' => null,
                ]);
        });

        $this->mock(GeminiTextService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('isConfigured')->andReturn(false);
        });

        $this->actingAs($user)
            ->postJson(route('admin.ai.content.generate-marketing-email'), [
                'marketing_group_id' => $group->id,
                'prompt' => 'Mention Friday',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('fields.subject', 'Book your spring website call')
            ->assertJsonPath('fields.body', '<p>Let us rebuild your site this spring.</p><p>Reply to book a Friday call.</p>');
    }

    public function test_email_compose_includes_the_ai_generate_button(): void
    {
        $user = $this->createAdminWithPermissions(['marketing.email.send']);
        $this->actingAs($user);

        $this->assertTrue(Route::has('admin.ai.content.generate-marketing-email'));

        $html = view('ai::components.generate-marketing-email-button')->render();

        $this->assertStringContainsString('ai-generate-marketing-email-trigger', $html);
        $this->assertStringContainsString(__('crm::marketing.ai.button'), $html);
    }
}

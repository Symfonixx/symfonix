<?php

namespace Tests\Feature\AI;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Modules\AI\Models\AiConversation;
use Tests\Concerns\InteractsWithAdminPermissions;
use Tests\TestCase;

class AssistantConversationTest extends TestCase
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

    public function test_user_without_permission_cannot_bootstrap_assistant(): void
    {
        $user = $this->createAdminWithPermissions();

        $this->actingAs($user)
            ->getJson(route('admin.ai.assistant.bootstrap'))
            ->assertForbidden();
    }

    public function test_user_with_permission_can_create_and_list_conversations(): void
    {
        $user = $this->createAdminWithPermissions(['ai.assistant.view']);

        $this->actingAs($user)
            ->postJson(route('admin.ai.assistant.conversations.store'))
            ->assertCreated()
            ->assertJsonPath('conversation.messages', []);

        $this->actingAs($user)
            ->getJson(route('admin.ai.assistant.conversations.index'))
            ->assertOk()
            ->assertJsonCount(1, 'conversations');
    }

    public function test_user_cannot_view_another_users_conversation(): void
    {
        $owner = $this->createAdminWithPermissions(['ai.assistant.view']);
        $other = $this->createAdminWithPermissions(['ai.assistant.view']);

        $conversation = AiConversation::query()->create([
            'user_id' => $owner->id,
            'title' => 'Owner chat',
        ]);

        $this->actingAs($other)
            ->getJson(route('admin.ai.assistant.conversations.show', $conversation))
            ->assertNotFound();
    }

    public function test_bootstrap_includes_visitor_sales_and_employee_suggestions(): void
    {
        $user = $this->createAdminWithPermissions([
            'ai.assistant.view',
            'overview.dashboard.view',
            'finance.invoices.view',
            'finance.dashboard.view',
            'crm.activities.view',
            'project.projects.view',
            'crm.leads.view',
            'hr.employees.view',
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('admin.ai.assistant.bootstrap'))
            ->assertOk();

        $ids = collect($response->json('suggestions'))->pluck('id');

        $this->assertContains('invoices', $ids);
        $this->assertContains('overdue_tasks', $ids);
        $this->assertContains('overdue_projects', $ids);
        $this->assertContains('revenue', $ids);
        $this->assertContains('growth', $ids);
        $this->assertContains('customers', $ids);
        $this->assertContains('leads', $ids);
        $this->assertContains('visitors', $ids);
        $this->assertContains('services', $ids);
        $this->assertContains('employees', $ids);
        $this->assertNotEmpty($response->json('suggestions.0.icon'));
    }

    public function test_assistant_drawer_renders_ask_symfonix_ai_branding(): void
    {
        $user = $this->createAdminWithPermissions(['ai.assistant.view']);
        $this->actingAs($user);

        $html = view('ai::components.ask-symfonix')->render();

        $this->assertStringContainsString('Ask Symfonix AI', $html);
        $this->assertStringContainsString('ask-symfonix-orb', $html);
        $this->assertStringContainsString('ask-symfonix-composer-form', $html);
        $this->assertStringContainsString('id="ask-symfonix-input"', $html);
    }
}

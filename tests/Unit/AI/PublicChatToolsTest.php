<?php

namespace Tests\Unit\AI;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Modules\AI\Services\Chatbot\Tools\CaptureWebsiteLeadTool;
use Modules\AI\Services\Chatbot\Tools\GetPublishedServiceTool;
use Modules\AI\Services\Chatbot\Tools\ListPublishedServicesTool;
use Modules\AI\Support\PublicChatContext;
use Modules\CRM\Models\Lead;
use Modules\Services\Enums\ServiceStatus;
use Modules\Services\Models\Service;
use Tests\TestCase;

class PublicChatToolsTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_tool_returns_only_published_services(): void
    {
        $this->makeService('Custom Web Apps', 'custom-web-apps', ServiceStatus::PUBLISHED);
        $this->makeService('Internal Draft', 'internal-draft', ServiceStatus::ARCHIVED);

        $result = app(ListPublishedServicesTool::class)->handle([], new PublicChatContext);

        $this->assertTrue($result->ok);
        $this->assertSame(1, $result->data['count']);
        $this->assertSame('Custom Web Apps', $result->data['services'][0]['title']);
        $this->assertSame('custom-web-apps', $result->data['services'][0]['slug']);
    }

    public function test_get_tool_finds_published_service_by_slug(): void
    {
        $service = $this->makeService('Mobile Apps', 'mobile-apps', ServiceStatus::PUBLISHED);

        $result = app(GetPublishedServiceTool::class)->handle(
            ['slug' => 'mobile-apps'],
            new PublicChatContext,
        );

        $this->assertTrue($result->ok);
        $this->assertSame($service->id, $result->data['id']);
        $this->assertSame('Mobile Apps', $result->data['title']);
        $this->assertNotEmpty($result->data['details'] ?? null);
    }

    public function test_get_tool_hides_unpublished_services(): void
    {
        $this->makeService('Secret', 'secret', ServiceStatus::ARCHIVED);

        $result = app(GetPublishedServiceTool::class)->handle(
            ['slug' => 'secret'],
            new PublicChatContext,
        );

        $this->assertTrue($result->ok);
        $this->assertTrue($result->data['empty'] ?? false);
    }

    public function test_capture_tool_requires_valid_email(): void
    {
        $result = app(CaptureWebsiteLeadTool::class)->handle([
            'name' => 'Ada Lovelace',
            'email' => 'not-an-email',
        ], new PublicChatContext);

        $this->assertTrue($result->denied);
        $this->assertSame(0, Lead::query()->count());
    }

    public function test_capture_tool_creates_a_website_lead(): void
    {
        Notification::fake();

        $service = $this->makeService('AI Automation', 'ai-automation', ServiceStatus::PUBLISHED);
        $context = new PublicChatContext(
            botmanUserId: 'user-1',
            botmanDriver: 'Web',
            locale: 'en',
            ipAddress: '127.0.0.1',
            transcript: [['role' => 'user', 'message' => 'Need automation']],
            problemStatement: 'Need automation',
        );

        $result = app(CaptureWebsiteLeadTool::class)->handle([
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'company_name' => 'Analytical Engines',
            'budget' => '20k',
            'service_id' => $service->id,
        ], $context);

        $this->assertTrue($result->ok);
        $this->assertTrue($result->data['captured']);
        $this->assertSame($context->leadId, $result->data['lead_id']);

        $lead = Lead::query()->first();
        $this->assertNotNull($lead);
        $this->assertSame('Ada Lovelace', $lead->name);
        $this->assertSame('ada@example.com', $lead->email);
        $this->assertSame(Lead::SOURCE_WEBSITE, $lead->source);
        $this->assertSame($service->id, $lead->service_id);
        $this->assertSame(['user-1'], [$lead->botman_user_id]);
    }

    public function test_arabic_web_query_ranks_web_services_first(): void
    {
        $this->makeService('Cloud Hosting', 'cloud-hosting', ServiceStatus::PUBLISHED);
        $this->makeService('Web & System Development', 'web-system-development', ServiceStatus::PUBLISHED);

        $result = app(ListPublishedServicesTool::class)->handle(
            ['query' => 'خدمات الويب'],
            new PublicChatContext(locale: 'ar'),
        );

        $this->assertTrue($result->ok);
        $this->assertGreaterThanOrEqual(2, $result->data['count']);
        $this->assertSame('Web & System Development', $result->data['services'][0]['title']);
    }

    private function makeService(string $title, string $slug, ServiceStatus $status): Service
    {
        return Service::query()->create([
            'title' => ['en' => $title],
            'slug' => $slug,
            'image' => 'services/placeholder.jpg',
            'description' => ['en' => $title.' summary'],
            'content' => ['en' => $title.' details for visitors.'],
            'status' => $status->value,
        ]);
    }
}

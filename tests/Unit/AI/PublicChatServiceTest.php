<?php

namespace Tests\Unit\AI;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Mockery;
use Modules\AI\Services\AiProviderRouter;
use Modules\AI\Services\Chatbot\PublicChatService;
use Modules\AI\Support\PublicChatContext;
use Modules\Services\Enums\ServiceStatus;
use Modules\Services\Models\Service;
use Tests\TestCase;

class PublicChatServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_is_unavailable_when_no_provider_is_configured(): void
    {
        $router = Mockery::mock(AiProviderRouter::class);
        $router->shouldReceive('isConfigured')->andReturn(false);
        $this->app->instance(AiProviderRouter::class, $router);

        $this->assertFalse(app(PublicChatService::class)->isAvailable());
    }

    public function test_it_replies_with_provider_content(): void
    {
        $this->bindRouter([
            [
                'success' => true,
                'content' => 'We can help you ship a web platform.',
                'tool_calls' => [],
                'error' => null,
                'provider' => 'openai',
            ],
        ]);

        $result = app(PublicChatService::class)->reply(
            'I need a new website',
            [],
            new PublicChatContext(ipAddress: '127.0.0.1'),
        );

        $this->assertTrue($result['success']);
        $this->assertSame('We can help you ship a web platform.', $result['reply']);
        $this->assertSame('openai', $result['provider']);
    }

    public function test_it_runs_published_service_tools_before_answering(): void
    {
        Service::query()->create([
            'title' => ['en' => 'Custom Web Apps'],
            'slug' => 'custom-web-apps',
            'image' => 'services/placeholder.jpg',
            'description' => ['en' => 'Laravel web platforms'],
            'content' => ['en' => 'We build Laravel web platforms.'],
            'status' => ServiceStatus::PUBLISHED->value,
        ]);

        $this->bindRouter([
            [
                'success' => true,
                'content' => '',
                'tool_calls' => [[
                    'id' => 'call_1',
                    'name' => 'list_published_services',
                    'arguments' => [],
                ]],
                'error' => null,
                'provider' => 'openai',
            ],
            [
                'success' => true,
                'content' => 'Custom Web Apps is a strong fit.',
                'tool_calls' => [],
                'error' => null,
                'provider' => 'openai',
            ],
        ]);

        $result = app(PublicChatService::class)->reply(
            'What do you offer?',
            [],
            new PublicChatContext(ipAddress: '10.0.0.2'),
        );

        $this->assertTrue($result['success']);
        $this->assertSame('Custom Web Apps is a strong fit.', $result['reply']);
    }

    public function test_it_rate_limits_repeat_visitors(): void
    {
        config(['ai.chatbot.rate_limit' => 1, 'ai.chatbot.rate_decay' => 60]);
        RateLimiter::clear('ai-chatbot:127.0.0.9');

        $this->bindRouter([
            [
                'success' => true,
                'content' => 'Hello',
                'tool_calls' => [],
                'error' => null,
                'provider' => 'openai',
            ],
        ]);

        $service = app(PublicChatService::class);
        $context = new PublicChatContext(ipAddress: '127.0.0.9');

        $first = $service->reply('Hi', [], $context);
        $second = $service->reply('Hi again', [], $context);

        $this->assertTrue($first['success']);
        $this->assertFalse($second['success']);
        $this->assertSame(__('chat.ai.rate_limit'), $second['reply']);
    }

    public function test_empty_provider_response_falls_back_to_published_services(): void
    {
        Service::query()->create([
            'title' => ['en' => 'Web & System Development', 'ar' => 'تطوير الويب والأنظمة'],
            'slug' => 'web-system-development',
            'image' => 'services/placeholder.jpg',
            'description' => ['en' => 'Custom web platforms', 'ar' => 'منصات ويب مخصصة'],
            'content' => ['en' => 'We build web platforms.', 'ar' => 'نبني منصات ويب.'],
            'status' => ServiceStatus::PUBLISHED->value,
        ]);

        $this->bindRouter([
            [
                'success' => false,
                'content' => null,
                'tool_calls' => [],
                'error' => __('ai::assistant.errors.empty'),
                'provider' => 'gemini',
            ],
        ]);

        $result = app(PublicChatService::class)->reply(
            'خدمات الويب',
            [],
            new PublicChatContext(locale: 'ar', ipAddress: '10.0.0.4'),
        );

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('تطوير الويب والأنظمة', $result['reply']);
        $this->assertNotEmpty($result['buttons']);
    }

    private function bindRouter(array $responses): void
    {
        $router = Mockery::mock(AiProviderRouter::class);
        $router->shouldReceive('isConfigured')->andReturn(true);
        $router->shouldReceive('chat')->andReturn(...$responses);
        $this->app->instance(AiProviderRouter::class, $router);
    }
}

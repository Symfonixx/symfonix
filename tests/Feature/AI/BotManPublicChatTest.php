<?php

namespace Tests\Feature\AI;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Modules\AI\Services\Chatbot\PublicChatService;
use Tests\TestCase;

class BotManPublicChatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([ValidateCsrfToken::class]);
    }

    public function test_botman_uses_ai_when_the_public_chat_is_available(): void
    {
        $service = Mockery::mock(PublicChatService::class);
        $service->shouldReceive('isAvailable')->andReturn(true);
        $service->shouldReceive('reply')->once()->andReturn([
            'success' => true,
            'reply' => 'We build custom web platforms for agencies.',
            'buttons' => [
                ['label' => 'Get a quote', 'value' => 'I would like a quote'],
            ],
            'lead_id' => null,
            'provider' => 'openai',
            'error' => null,
        ]);
        $this->app->instance(PublicChatService::class, $service);

        $response = $this->post('/botman', [
            'driver' => 'web',
            'userId' => 'user-ai-1',
            'message' => 'What can you help with?',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['text' => 'We build custom web platforms for agencies.'])
            ->assertJsonFragment(['text' => 'Get a quote']);
    }

    public function test_botman_falls_back_to_lead_flow_when_ai_is_unavailable(): void
    {
        $service = Mockery::mock(PublicChatService::class);
        $service->shouldReceive('isAvailable')->andReturn(false);
        $service->shouldReceive('reply')->never();
        $this->app->instance(PublicChatService::class, $service);

        $response = $this->post('/botman', [
            'driver' => 'web',
            'userId' => 'user-fallback-1',
            'message' => 'Hello',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['text' => __('chat.lead.greeting')])
            ->assertJsonFragment(['text' => __('chat.lead.ask_problem')]);
    }
}

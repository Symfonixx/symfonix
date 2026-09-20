<?php

namespace Tests\Unit\Marketing;

use Modules\CRM\Models\WhatsAppTemplate;
use Modules\CRM\Services\Marketing\WhatsAppApiService;
use Modules\CRM\Services\Marketing\WhatsAppTemplateService;
use Tests\TestCase;

class WhatsAppTemplateVariableTest extends TestCase
{
    public function test_it_collects_header_body_and_url_variables_separately(): void
    {
        $template = $this->template();
        $variables = app(WhatsAppTemplateService::class)->collectVariables($template);

        $this->assertCount(4, $variables);
        $this->assertSame('header', $variables[0]['component']);
        $this->assertSame(1, $variables[0]['index']);
        $this->assertSame('body', $variables[1]['component']);
        $this->assertSame(1, $variables[1]['index']);
        $this->assertSame('body', $variables[2]['component']);
        $this->assertSame(2, $variables[2]['index']);
        $this->assertSame('buttons', $variables[3]['component']);
        $this->assertSame(0, $variables[3]['button_index']);
        $this->assertSame(1, $variables[3]['index']);
        $this->assertSame(2, $template->bodyVariableCount());
        $this->assertSame(4, $template->variableCount());
    }

    public function test_it_renders_preview_with_independent_header_and_url_values(): void
    {
        $preview = app(WhatsAppTemplateService::class)->renderPreview($this->template(), [
            'header' => [1 => 'Hadi'],
            'body' => [1 => 'Q-100', 2 => 'Friday'],
            'buttons' => [0 => [1 => 'q-100']],
        ]);

        $this->assertStringContainsString('Hello Hadi', $preview);
        $this->assertStringContainsString('Order Q-100 ships Friday', $preview);
        $this->assertStringContainsString('[Track] https://example.com/q-100', $preview);
        $this->assertStringNotContainsString('{{1}}', $preview);
    }

    public function test_legacy_flat_parameters_still_fill_body_variables(): void
    {
        $template = new WhatsAppTemplate([
            'header_type' => WhatsAppTemplate::HEADER_NONE,
            'body' => 'Hello {{1}}, get {{2}}% off!',
        ]);

        $preview = app(WhatsAppTemplateService::class)->renderPreview($template, [
            1 => 'Hadi',
            2 => '25',
        ]);

        $this->assertSame('Hello Hadi, get 25% off!', $preview);
    }

    public function test_it_builds_api_components_for_header_and_url_variables(): void
    {
        $components = app(WhatsAppApiService::class)->buildComponents($this->template(), [
            'header' => [1 => 'Hadi'],
            'body' => [1 => 'Q-100', 2 => 'Friday'],
            'buttons' => [0 => [1 => 'q-100']],
        ]);

        $this->assertSame([
            [
                'type' => 'header',
                'parameters' => [
                    ['type' => 'text', 'text' => 'Hadi'],
                ],
            ],
            [
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => 'Q-100'],
                    ['type' => 'text', 'text' => 'Friday'],
                ],
            ],
            [
                'type' => 'button',
                'sub_type' => 'url',
                'index' => '0',
                'parameters' => [
                    ['type' => 'text', 'text' => 'q-100'],
                ],
            ],
        ], $components);
    }

    public function test_static_text_header_omits_header_component(): void
    {
        $template = new WhatsAppTemplate([
            'header_type' => WhatsAppTemplate::HEADER_TEXT,
            'header_content' => 'Seasonal offer',
            'body' => 'Hello there',
            'buttons' => [
                ['type' => 'URL', 'text' => 'Shop', 'url' => 'https://example.com/shop'],
            ],
        ]);

        $this->assertSame([], app(WhatsAppApiService::class)->buildComponents($template, []));
    }

    public function test_media_header_sends_the_stored_link(): void
    {
        $template = new WhatsAppTemplate([
            'header_type' => WhatsAppTemplate::HEADER_IMAGE,
            'header_content' => 'https://cdn.example.com/banner.jpg',
            'body' => 'Hello there',
        ]);

        $components = app(WhatsAppApiService::class)->buildComponents($template, []);

        $this->assertSame([
            [
                'type' => 'header',
                'parameters' => [
                    [
                        'type' => 'image',
                        'image' => ['link' => 'https://cdn.example.com/banner.jpg'],
                    ],
                ],
            ],
        ], $components);
    }

    private function template(): WhatsAppTemplate
    {
        return new WhatsAppTemplate([
            'name' => 'order_ready',
            'language' => 'en',
            'header_type' => WhatsAppTemplate::HEADER_TEXT,
            'header_content' => 'Hello {{1}}',
            'body' => 'Order {{1}} ships {{2}}',
            'buttons' => [
                ['type' => 'URL', 'text' => 'Track', 'url' => 'https://example.com/{{1}}'],
            ],
        ]);
    }
}

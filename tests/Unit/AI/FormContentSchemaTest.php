<?php

namespace Tests\Unit\AI;

use Modules\AI\Support\FormContentSchema;
use PHPUnit\Framework\TestCase;

class FormContentSchemaTest extends TestCase
{
    public function test_it_decodes_json_wrapped_in_fences(): void
    {
        $decoded = FormContentSchema::decode("```json\n{\"title\":\"Hello\",\"slug\":\"hello\"}\n```");

        $this->assertSame('Hello', $decoded['title'] ?? null);
        $this->assertSame('hello', $decoded['slug'] ?? null);
    }

    public function test_it_decodes_json_embedded_in_prose(): void
    {
        $decoded = FormContentSchema::decode('Here you go: {"title":"Hello"} thanks');

        $this->assertSame('Hello', $decoded['title'] ?? null);
    }

    public function test_it_unwraps_nested_form_json(): void
    {
        $decoded = FormContentSchema::decode(json_encode([
            'fields' => [
                'title' => 'Nested Title',
                'content' => '<p>Body</p>',
            ],
        ]));

        $this->assertSame('Nested Title', $decoded['title'] ?? null);
        $this->assertSame('<p>Body</p>', $decoded['content'] ?? null);
    }

    public function test_it_unwraps_json_arrays_of_objects(): void
    {
        $decoded = FormContentSchema::decode('[{"title":"From List","slug":"from-list"}]');

        $this->assertSame('From List', $decoded['title'] ?? null);
        $this->assertSame('from-list', $decoded['slug'] ?? null);
    }

    public function test_it_normalizes_blog_fields(): void
    {
        $fields = FormContentSchema::normalize('cms_blog', [
            'title' => '  Laravel Modular Architecture  ',
            'slug' => 'Laravel 13 Modular Architecture!',
            'description' => str_repeat('A', 200),
            'keywords' => ['Laravel', ['value' => 'CMS'], ''],
            'content' => "```html\n<p>Intro</p>\n```",
        ]);

        $this->assertSame('Laravel Modular Architecture', $fields['title']);
        $this->assertSame('laravel-13-modular-architecture', $fields['slug']);
        $this->assertSame(160, mb_strlen($fields['description']));
        $this->assertSame('Laravel, CMS', $fields['keywords']);
        $this->assertSame('<p>Intro</p>', $fields['content']);
        $this->assertTrue(FormContentSchema::hasContent($fields));
    }

    public function test_it_normalizes_use_case_fields(): void
    {
        $fields = FormContentSchema::normalize('use_case', [
            'title' => 'Retail POS Rollout',
            'slug' => 'Retail POS Rollout',
            'client_name' => '  Acme Retail  ',
            'summary' => 'A store-wide POS upgrade.',
            'technologies' => ['Laravel', 'Vue'],
        ]);

        $this->assertSame('Retail POS Rollout', $fields['title']);
        $this->assertSame('retail-pos-rollout', $fields['slug']);
        $this->assertSame('Acme Retail', $fields['client_name']);
        $this->assertSame('Laravel, Vue', $fields['technologies']);
    }

    public function test_it_includes_website_seo_fields_for_products(): void
    {
        $fields = FormContentSchema::normalize('product', [
            'name' => 'Cloud Hosting',
            'seo_title' => 'Cloud Hosting for Agencies',
            'seo_keywords' => ['hosting', 'cloud'],
        ]);

        $this->assertSame('Cloud Hosting', $fields['name']);
        $this->assertSame('Cloud Hosting for Agencies', $fields['seo_title']);
        $this->assertSame('hosting, cloud', $fields['seo_keywords']);
        $this->assertArrayHasKey('description', $fields);
        $this->assertArrayHasKey('short_description', $fields);
    }

    public function test_optimize_user_message_includes_existing_fields(): void
    {
        $message = FormContentSchema::userMessage('Make it shorter', ['title' => 'Old Title'], FormContentSchema::MODE_OPTIMIZE);

        $this->assertStringContainsString('Optimize this existing form content', $message);
        $this->assertStringContainsString('Old Title', $message);
        $this->assertStringContainsString('Make it shorter', $message);
    }

    public function test_create_user_message_includes_existing_fields(): void
    {
        $message = FormContentSchema::userMessage('A landing page', ['title' => 'Draft title']);

        $this->assertStringContainsString('Topic / brief', $message);
        $this->assertStringContainsString('A landing page', $message);
        $this->assertStringContainsString('Draft title', $message);
    }

    public function test_it_filters_blank_existing_values(): void
    {
        $this->assertSame(
            ['title' => 'Hello'],
            FormContentSchema::filledExisting([
                'title' => '  Hello  ',
                'slug' => '   ',
                0 => 'ignored',
                'count' => 12,
            ]),
        );
    }

    public function test_it_maps_form_types_to_catalog_permissions(): void
    {
        $this->assertSame(['cms.blogs.create', 'cms.blogs.edit'], FormContentSchema::permissions('cms_blog'));
        $this->assertSame(['product.catalog.create', 'product.catalog.edit'], FormContentSchema::permissions('product'));
        $this->assertSame([], FormContentSchema::permissions('unknown'));
    }

    public function test_it_normalizes_unknown_modes_to_create(): void
    {
        $this->assertSame(FormContentSchema::MODE_CREATE, FormContentSchema::normalizeMode('rewrite'));
        $this->assertSame(FormContentSchema::MODE_OPTIMIZE, FormContentSchema::normalizeMode('optimize'));
        $this->assertSame([FormContentSchema::MODE_CREATE, FormContentSchema::MODE_OPTIMIZE], FormContentSchema::modes());
    }

    public function test_it_returns_empty_array_for_unknown_forms(): void
    {
        $this->assertSame([], FormContentSchema::normalize('unknown', ['title' => 'X']));
        $this->assertFalse(FormContentSchema::hasContent(['title' => '', 'body' => '']));
    }
}

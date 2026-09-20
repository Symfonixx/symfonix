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

    public function test_it_returns_empty_array_for_unknown_forms(): void
    {
        $this->assertSame([], FormContentSchema::normalize('unknown', ['title' => 'X']));
    }
}

<?php

namespace Tests\Feature\AI;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Mockery\MockInterface;
use Modules\AI\Services\GeminiTextService;
use Modules\AI\Services\OpenAITextService;
use Tests\Concerns\InteractsWithAdminPermissions;
use Tests\TestCase;

class FormContentGenerationTest extends TestCase
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

    public function test_user_without_permission_cannot_generate_form_content(): void
    {
        $user = $this->createAdminWithPermissions();

        $this->actingAs($user)
            ->postJson(route('admin.ai.content.generate-form'), [
                'form_type' => 'cms_blog',
                'prompt' => 'A post about Laravel modules',
            ])
            ->assertForbidden();
    }

    public function test_user_cannot_generate_a_form_type_they_cannot_edit(): void
    {
        $user = $this->createAdminWithPermissions(['cms.blogs.create']);

        $this->actingAs($user)
            ->postJson(route('admin.ai.content.generate-form'), [
                'form_type' => 'product',
                'prompt' => 'A hosting plan',
            ])
            ->assertForbidden();
    }

    public function test_optimize_mode_requires_existing_content(): void
    {
        $user = $this->createAdminWithPermissions(['cms.blogs.edit']);

        $this->actingAs($user)
            ->postJson(route('admin.ai.content.generate-form'), [
                'form_type' => 'cms_blog',
                'mode' => 'optimize',
                'existing' => ['title' => '   '],
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['existing']);
    }

    public function test_it_generates_blog_form_fields(): void
    {
        $user = $this->createAdminWithPermissions(['cms.blogs.create']);

        $this->mock(OpenAITextService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('generateStructured')
                ->once()
                ->andReturn([
                    'success' => true,
                    'content' => json_encode([
                        'title' => 'Laravel Modules in Production',
                        'slug' => 'laravel-modules-in-production',
                        'description' => 'How agencies ship Laravel modules without slowing delivery.',
                        'keywords' => 'Laravel, modules, CMS',
                        'content' => '<p>Ship smaller, safer releases.</p>',
                    ]),
                    'error' => null,
                ]);
        });

        $this->mock(GeminiTextService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('isConfigured')->andReturn(false);
        });

        $this->actingAs($user)
            ->postJson(route('admin.ai.content.generate-form'), [
                'form_type' => 'cms_blog',
                'prompt' => 'Laravel modules for agencies',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('fields.title', 'Laravel Modules in Production')
            ->assertJsonPath('fields.slug', 'laravel-modules-in-production')
            ->assertJsonPath('fields.keywords', 'Laravel, modules, CMS')
            ->assertJsonPath('fields.content', '<p>Ship smaller, safer releases.</p>');
    }

    public function test_it_optimizes_existing_form_fields_from_wrapped_json(): void
    {
        $user = $this->createAdminWithPermissions(['services.catalog.edit']);

        $this->mock(OpenAITextService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('generateStructured')
                ->once()
                ->andReturn([
                    'success' => true,
                    'content' => json_encode([
                        'fields' => [
                            'title' => 'Digital Marketing',
                            'slug' => 'digital-marketing',
                            'description' => 'SEO and paid campaigns for growing brands.',
                            'keywords' => 'SEO, ads, marketing',
                            'content' => '<p>Clearer service copy.</p>',
                        ],
                    ]),
                    'error' => null,
                ]);
        });

        $this->mock(GeminiTextService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('isConfigured')->andReturn(false);
        });

        $this->actingAs($user)
            ->postJson(route('admin.ai.content.generate-form'), [
                'form_type' => 'service',
                'mode' => 'optimize',
                'existing' => [
                    'title' => 'Marketing',
                    'content' => '<p>Old copy.</p>',
                ],
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('fields.title', 'Digital Marketing')
            ->assertJsonPath('fields.content', '<p>Clearer service copy.</p>');
    }

    public function test_form_includes_the_ai_generate_button(): void
    {
        $this->assertTrue(Route::has('admin.ai.content.generate-form'));

        $html = view('ai::components.generate-form-button', [
            'type' => 'cms_blog',
            'banner' => true,
        ])->render();

        $this->assertStringContainsString('ai-generate-form-trigger', $html);
        $this->assertStringContainsString(__('Generate with AI'), $html);
    }
}

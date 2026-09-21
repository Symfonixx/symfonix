<?php

namespace Tests\Feature\AI;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Mockery\MockInterface;
use Modules\AI\Services\GeminiImageService;
use Modules\Cms\Enums\CmsStatus;
use Modules\Cms\Models\Blog;
use Modules\Cms\Models\BlogCategory;
use Tests\Concerns\InteractsWithAdminPermissions;
use Tests\TestCase;

class ImageEditGenerationTest extends TestCase
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

    public function test_user_without_permission_cannot_generate_an_image(): void
    {
        $user = $this->createAdminWithPermissions();

        $this->actingAs($user)
            ->postJson(route('admin.ai.image-edits.generate'), [
                'prompt' => 'A blue abstract cover',
                'mode' => 'create',
            ])
            ->assertForbidden();
    }

    public function test_user_cannot_edit_a_target_they_cannot_manage(): void
    {
        $user = $this->createAdminWithPermissions(['cms.blogs.create']);
        $blog = $this->makeBlog();

        $this->actingAs($user)
            ->postJson(route('admin.ai.image-edits.generate'), [
                'prompt' => 'Make it darker',
                'mode' => 'edit',
                'target' => 'cms_blog',
                'id' => $blog->id,
                'field' => 'image',
            ])
            ->assertForbidden();
    }

    public function test_it_rejects_an_unknown_image_field(): void
    {
        $user = $this->createAdminWithPermissions(['cms.blogs.edit']);
        $blog = $this->makeBlog();

        $this->actingAs($user)
            ->postJson(route('admin.ai.image-edits.generate'), [
                'prompt' => 'Make it darker',
                'mode' => 'create',
                'target' => 'cms_blog',
                'id' => $blog->id,
                'field' => 'not_a_field',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['field']);
    }

    public function test_it_generates_an_adhoc_image(): void
    {
        $user = $this->createAdminWithPermissions(['cms.blogs.create']);

        $this->mock(GeminiImageService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('generateImage')
                ->once()
                ->andReturn([
                    'success' => true,
                    'binary' => 'imagedata',
                    'mime_type' => 'image/png',
                    'error' => null,
                ]);
        });

        $this->actingAs($user)
            ->postJson(route('admin.ai.image-edits.generate'), [
                'prompt' => 'A blue abstract cover',
                'mode' => 'create',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('mode', 'adhoc')
            ->assertJsonPath('previewUrl', 'data:image/png;base64,'.base64_encode('imagedata'));
    }

    public function test_it_applies_a_persisted_image_edit(): void
    {
        Storage::fake('public');

        $user = $this->createAdminWithPermissions(['cms.blogs.edit']);
        $blog = $this->makeBlog('blogs/original.png');
        Storage::disk('public')->put('blogs/original.png', 'old');
        Storage::disk('public')->put('ai-edits/cms_blog/'.$blog->id.'/new.png', 'new');

        $token = Crypt::encrypt([
            'path' => 'ai-edits/cms_blog/'.$blog->id.'/new.png',
            'disk' => 'public',
            'target' => 'cms_blog',
            'id' => $blog->id,
            'field' => 'image',
            'expires_at' => now()->addMinutes(30)->timestamp,
        ]);

        $this->actingAs($user)
            ->postJson(route('admin.ai.image-edits.apply'), [
                'token' => $token,
                'action' => 'replace',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSame('ai-edits/cms_blog/'.$blog->id.'/new.png', $blog->fresh()->image);
        $this->assertFalse(Storage::disk('public')->exists('blogs/original.png'));
    }

    public function test_it_rejects_an_expired_apply_token(): void
    {
        $user = $this->createAdminWithPermissions(['cms.blogs.edit']);

        $token = Crypt::encrypt([
            'path' => 'missing.png',
            'disk' => 'public',
            'target' => 'cms_blog',
            'id' => 1,
            'field' => 'image',
            'expires_at' => now()->subMinute()->timestamp,
        ]);

        $this->actingAs($user)
            ->postJson(route('admin.ai.image-edits.apply'), [
                'token' => $token,
                'action' => 'replace',
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    private function makeBlog(string $image = 'blogs/hello.png'): Blog
    {
        $category = BlogCategory::query()->create([
            'name' => ['en' => 'News'],
            'slug' => 'news-'.uniqid(),
        ]);

        return Blog::query()->create([
            'title' => ['en' => 'Hello'],
            'slug' => 'hello-'.uniqid(),
            'category_id' => $category->id,
            'description' => ['en' => 'A short description'],
            'content' => ['en' => 'Body'],
            'image' => $image,
            'status' => CmsStatus::PUBLISHED->value,
            'keywords' => ['en' => 'news'],
        ]);
    }
}

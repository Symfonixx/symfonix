<?php

namespace Tests\Feature\CMS;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdminPermissions;
use Tests\TestCase;

class BlogCategoryValidationTest extends TestCase
{
    use InteractsWithAdminPermissions;
    use RefreshDatabase;

    public function test_blog_category_store_requires_name_and_unique_slug(): void
    {
        $admin = $this->createAdminWithPermissions(['cms.blog_categories.create']);

        $this->actingAs($admin)
            ->from(route('admin.blogs_categories.create'))
            ->post(route('admin.blogs_categories.store'), [
                'name' => '',
                'slug' => '',
            ])
            ->assertRedirect(route('admin.blogs_categories.create'))
            ->assertSessionHasErrors(['name', 'slug']);
    }
}

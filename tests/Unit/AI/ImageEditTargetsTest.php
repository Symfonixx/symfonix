<?php

namespace Tests\Unit\AI;

use Modules\AI\Support\ImageEditTargets;
use Tests\TestCase;

class ImageEditTargetsTest extends TestCase
{
    public function test_it_exposes_registered_targets_and_permissions(): void
    {
        $this->assertContains('product', ImageEditTargets::names());
        $this->assertContains('cms_blog', ImageEditTargets::names());
        $this->assertTrue(ImageEditTargets::allowsField('product', 'main_image'));
        $this->assertFalse(ImageEditTargets::allowsField('product', 'sku'));
        $this->assertContains('product.catalog.edit', ImageEditTargets::anyPermissions());
        $this->assertContains('product.catalog.create', ImageEditTargets::anyPermissions());
        $this->assertContains('cms.blogs.create', ImageEditTargets::anyPermissions());
    }
}

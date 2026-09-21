<?php

namespace Tests\Unit\Cms;

use Modules\Cms\Data\BlogData;
use Modules\Cms\Enums\CmsStatus;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class BlogDataTest extends TestCase
{
    public function test_status_uses_cms_status_not_service_status(): void
    {
        $type = (new ReflectionProperty(BlogData::class, 'status'))->getType();

        $this->assertNotNull($type);
        $this->assertSame(CmsStatus::class, $type->getName());
    }
}

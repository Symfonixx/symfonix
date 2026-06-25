<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Product\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    /**
     * Seed product categories tailored for tech companies.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'SaaS Platforms',
                'slug' => 'saas-platforms',
                'description' => 'Subscription-based software products and cloud applications.',
            ],
            [
                'name' => 'Professional Services',
                'slug' => 'professional-services',
                'description' => 'Custom development, consulting, implementation, and technical delivery.',
            ],
            [
                'name' => 'API & Integrations',
                'slug' => 'api-integrations',
                'description' => 'API access, webhooks, SDKs, and third-party integration packages.',
            ],
            [
                'name' => 'Infrastructure & DevOps',
                'slug' => 'infrastructure-devops',
                'description' => 'Cloud hosting, CI/CD, monitoring, and managed infrastructure.',
            ],
        ];

        foreach ($categories as $category) {
            ProductCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}

<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;

class TechProductSeeder extends Seeder
{
    /**
     * Seed a tech-company product catalog with realistic offerings.
     */
    public function run(): void
    {
        $categories = ProductCategory::query()->pluck('id', 'slug');

        $products = [
            [
                'category' => 'saas-platforms',
                'name' => 'Symfonix CRM Pro',
                'sku' => 'SFX-CRM-PRO',
                'description' => 'Full-featured CRM with pipeline management, automation, and analytics for growing tech teams.',
                'price' => 99.00,
                'currency' => 'USD',
                'billing_type' => Product::BILLING_MONTHLY,
                'is_featured' => true,
            ],
            [
                'category' => 'saas-platforms',
                'name' => 'Analytics Dashboard Suite',
                'sku' => 'SFX-ANL-DSH',
                'description' => 'Real-time business intelligence dashboards with custom KPI tracking.',
                'price' => 179.00,
                'currency' => 'EUR',
                'billing_type' => Product::BILLING_MONTHLY,
                'is_featured' => false,
            ],
            [
                'category' => 'saas-platforms',
                'name' => 'White-Label Platform License',
                'sku' => 'SFX-WL-LIC',
                'description' => 'Annual white-label license for agencies reselling under their own brand.',
                'price' => 9500.00,
                'currency' => 'GBP',
                'billing_type' => Product::BILLING_YEARLY,
                'is_featured' => true,
            ],
            [
                'category' => 'professional-services',
                'name' => 'Custom Software Development',
                'sku' => 'SFX-DEV-CUSTOM',
                'description' => 'End-to-end bespoke application development — discovery through deployment.',
                'price' => 15000.00,
                'currency' => 'USD',
                'billing_type' => Product::BILLING_ONE_TIME,
                'is_featured' => true,
            ],
            [
                'category' => 'professional-services',
                'name' => 'Mobile App Development',
                'sku' => 'SFX-DEV-MOBILE',
                'description' => 'Native and cross-platform mobile apps for iOS and Android.',
                'price' => 22000.00,
                'currency' => 'EUR',
                'billing_type' => Product::BILLING_ONE_TIME,
                'is_featured' => false,
            ],
            [
                'category' => 'professional-services',
                'name' => 'Technical Support Retainer',
                'sku' => 'SFX-SUP-RET',
                'description' => 'Dedicated monthly support hours with SLA-backed response times.',
                'price' => 1500.00,
                'currency' => 'USD',
                'billing_type' => Product::BILLING_MONTHLY,
                'is_featured' => false,
            ],
            [
                'category' => 'professional-services',
                'name' => 'Security Audit Package',
                'sku' => 'SFX-SEC-AUDIT',
                'description' => 'Penetration testing, vulnerability assessment, and remediation roadmap.',
                'price' => 275000.00,
                'currency' => 'TRY',
                'billing_type' => Product::BILLING_ONE_TIME,
                'is_featured' => false,
            ],
            [
                'category' => 'api-integrations',
                'name' => 'Enterprise API Access',
                'sku' => 'SFX-API-ENT',
                'description' => 'High-volume REST API with webhooks, rate limits, and dedicated support.',
                'price' => 499.00,
                'currency' => 'USD',
                'billing_type' => Product::BILLING_MONTHLY,
                'is_featured' => true,
            ],
            [
                'category' => 'api-integrations',
                'name' => 'Payment Gateway Integration',
                'sku' => 'SFX-INT-PAY',
                'description' => 'One-time setup for Stripe, PayPal, or regional payment provider integration.',
                'price' => 3200.00,
                'currency' => 'EUR',
                'billing_type' => Product::BILLING_ONE_TIME,
                'is_featured' => false,
            ],
            [
                'category' => 'infrastructure-devops',
                'name' => 'Cloud Hosting Package',
                'sku' => 'SFX-INF-CLOUD',
                'description' => 'Managed cloud hosting with auto-scaling, backups, and 99.9% uptime SLA.',
                'price' => 299.00,
                'currency' => 'USD',
                'billing_type' => Product::BILLING_MONTHLY,
                'is_featured' => false,
            ],
            [
                'category' => 'infrastructure-devops',
                'name' => 'DevOps & CI/CD Setup',
                'sku' => 'SFX-INF-DEVOPS',
                'description' => 'Pipeline setup with Docker, Kubernetes, GitHub Actions, and monitoring.',
                'price' => 4200.00,
                'currency' => 'GBP',
                'billing_type' => Product::BILLING_ONE_TIME,
                'is_featured' => false,
            ],
        ];

        foreach ($products as $item) {
            $categoryId = $categories[$item['category']] ?? null;

            if ($categoryId === null) {
                continue;
            }

            Product::query()->updateOrCreate(
                ['sku' => $item['sku']],
                [
                    'product_category_id' => $categoryId,
                    'name' => $item['name'],
                    'slug' => Str::slug($item['name']),
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'currency' => $item['currency'],
                    'billing_type' => $item['billing_type'],
                    'status' => Product::STATUS_ACTIVE,
                    'is_featured' => $item['is_featured'],
                ]
            );
        }
    }
}

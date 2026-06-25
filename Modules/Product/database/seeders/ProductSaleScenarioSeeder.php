<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CRM\Models\Company;
use Modules\Finance\Services\FinanceService;
use Modules\Product\Models\Product;

class ProductSaleScenarioSeeder extends Seeder
{
    /**
     * Seed demo product sales with matching finance ledger income entries.
     */
    public function run(): void
    {
        $financeService = app(FinanceService::class);
        $company = Company::query()->orderBy('id')->first();

        $scenarios = [
            ['sku' => 'SFX-CRM-PRO', 'quantity' => 5, 'days_ago' => 2],
            ['sku' => 'SFX-API-ENT', 'quantity' => 1, 'days_ago' => 5],
            ['sku' => 'SFX-DEV-CUSTOM', 'quantity' => 1, 'days_ago' => 10],
            ['sku' => 'SFX-INF-CLOUD', 'quantity' => 3, 'days_ago' => 14],
            ['sku' => 'SFX-WL-LIC', 'quantity' => 1, 'days_ago' => 21],
            ['sku' => 'SFX-SEC-AUDIT', 'quantity' => 1, 'days_ago' => 28],
        ];

        foreach ($scenarios as $scenario) {
            $product = Product::query()->where('sku', $scenario['sku'])->first();

            if ($product === null) {
                continue;
            }

            $soldAt = now()->subDays($scenario['days_ago'])->toDateString();

            $financeService->recordProductSale([
                'product_id' => $product->id,
                'company_id' => $company?->id,
                'quantity' => $scenario['quantity'],
                'sold_at' => $soldAt,
                'notes' => 'Seeded tech-company product sale scenario.',
            ]);
        }
    }
}

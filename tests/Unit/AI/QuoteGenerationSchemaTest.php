<?php

namespace Tests\Unit\AI;

use Modules\AI\Support\QuoteGenerationSchema;
use Tests\TestCase;

class QuoteGenerationSchemaTest extends TestCase
{
    public function test_it_keeps_catalog_ids_and_clamps_values(): void
    {
        $fields = QuoteGenerationSchema::normalize([
            'terms' => '  <p>Payment due in 14 days.</p>  ',
            'notes' => 'Scope covers the website rebuild.',
            'validity_days' => 120,
            'currency' => 'eur',
            'lines' => [
                [
                    'item_type' => 'service',
                    'service_id' => 4,
                    'quantity' => 0,
                    'unit_price' => -10,
                    'discount_percent' => 150,
                    'tax_percent' => 8,
                    'description' => str_repeat('A', 300),
                ],
            ],
        ], $this->catalog(), [], 'USD', 0);

        $this->assertSame('Payment due in 14 days.', $fields['terms']);
        $this->assertSame('EUR', $fields['currency']);
        $this->assertSame(90, $fields['validity_days']);
        $this->assertCount(1, $fields['lines']);
        $this->assertSame(4, $fields['lines'][0]['service_id']);
        $this->assertNull($fields['lines'][0]['product_id']);
        $this->assertSame(1, $fields['lines'][0]['quantity']);
        $this->assertSame(0.0, $fields['lines'][0]['unit_price']);
        $this->assertSame(100.0, $fields['lines'][0]['discount_percent']);
        $this->assertSame(255, mb_strlen($fields['lines'][0]['description']));
    }

    public function test_it_matches_invalid_ids_by_description_and_falls_back_to_deal_lines(): void
    {
        $matched = QuoteGenerationSchema::normalize([
            'lines' => [
                [
                    'item_type' => 'service',
                    'service_id' => 999,
                    'description' => 'Website rebuild for the launch',
                    'quantity' => 2,
                    'unit_price' => 2500,
                ],
            ],
        ], $this->catalog(), [], 'USD', 5);

        $this->assertSame(4, $matched['lines'][0]['service_id']);
        $this->assertSame(5.0, $matched['lines'][0]['tax_percent']);

        $fallback = QuoteGenerationSchema::normalize(
            ['lines' => [['item_type' => 'service', 'service_id' => 999, 'description' => 'Unknown']]],
            $this->catalog(),
            [[
                'item_type' => 'service',
                'service_id' => 4,
                'description' => 'Website rebuild',
                'quantity' => 1,
                'unit_price' => 4000,
                'discount_percent' => 0,
                'tax_percent' => 0,
            ]],
            'USD',
            0,
        );

        $this->assertSame(4, $fallback['lines'][0]['service_id']);
        $this->assertSame(4000.0, $fallback['lines'][0]['unit_price']);
    }

    public function test_it_normalizes_product_lines_and_uses_deal_currency_when_invalid(): void
    {
        $fields = QuoteGenerationSchema::normalize([
            'currency' => 'US',
            'lines' => [
                [
                    'item_type' => 'product',
                    'product_id' => 9,
                    'quantity' => 3,
                    'unit_price' => 199,
                ],
            ],
        ], $this->catalog(), [], 'USD', 0);

        $this->assertSame('USD', $fields['currency']);
        $this->assertSame('product', $fields['lines'][0]['item_type']);
        $this->assertSame(9, $fields['lines'][0]['product_id']);
        $this->assertNull($fields['lines'][0]['service_id']);
        $this->assertSame(18.0, $fields['lines'][0]['tax_percent']);
        $this->assertSame('Hosting plan', $fields['lines'][0]['description']);
    }

    /**
     * @return array{services: array<int, array<string, mixed>>, products: array<int, array<string, mixed>>}
     */
    private function catalog(): array
    {
        return [
            'services' => [
                4 => ['id' => 4, 'title' => 'Website rebuild'],
            ],
            'products' => [
                9 => ['id' => 9, 'name' => 'Hosting plan', 'tax_percent' => 18],
            ],
        ];
    }
}

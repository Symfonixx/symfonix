<?php

namespace Tests\Unit\Finance;

use Modules\Tax\Models\TaxRate;
use Modules\Tax\Services\TaxCalculationService;
use PHPUnit\Framework\TestCase;

class TaxCalculationServiceTest extends TestCase
{
    public function test_it_calculates_exclusive_tax_line_amounts(): void
    {
        $service = new TaxCalculationService;
        $taxRate = new TaxRate([
            'percentage' => 15,
            'type' => TaxRate::TYPE_EXCLUSIVE,
        ]);

        $result = $service->calculateLine(2, 100, $taxRate);

        $this->assertSame(200.0, $result['base']);
        $this->assertSame(200.0, $result['subtotal']);
        $this->assertSame(15.0, $result['tax_percent']);
        $this->assertSame(30.0, $result['tax_amount']);
        $this->assertSame(230.0, $result['amount']);
    }

    public function test_it_calculates_inclusive_tax_line_amounts(): void
    {
        $service = new TaxCalculationService;
        $taxRate = new TaxRate([
            'percentage' => 15,
            'type' => TaxRate::TYPE_INCLUSIVE,
        ]);

        $result = $service->calculateLine(1, 115, $taxRate);

        $this->assertSame(115.0, $result['base']);
        $this->assertSame(100.0, $result['subtotal']);
        $this->assertSame(15.0, $result['tax_percent']);
        $this->assertSame(15.0, $result['tax_amount']);
        $this->assertSame(115.0, $result['amount']);
    }

    public function test_it_applies_document_level_discounts_and_rounding(): void
    {
        $service = new TaxCalculationService;

        $result = $service->calculateDocument([
            [
                'description' => 'Line A',
                'quantity' => 3,
                'unit_price' => 10,
                'discount_percent' => 10,
                'tax_percent' => 5,
            ],
            [
                'description' => 'Line B',
                'quantity' => 2,
                'unit_price' => 40,
                'discount_percent' => 0,
                'tax_percent' => 0,
            ],
        ]);

        $this->assertSame(107.0, $result['subtotal']);
        $this->assertSame(1.35, $result['tax_amount']);
        $this->assertSame(108.35, $result['total']);
        $this->assertCount(2, $result['lines']);
    }
}

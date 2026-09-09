<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Product\Models\Product;
use Modules\Services\Models\Service;

class QuoteLine extends Model
{
    public const TYPE_SERVICE = 'service';

    public const TYPE_PRODUCT = 'product';

    protected $fillable = [
        'quote_id',
        'service_id',
        'product_id',
        'item_type',
        'description',
        'quantity',
        'unit_price',
        'discount_percent',
        'tax_percent',
        'discount_amount',
        'tax_amount',
        'amount',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'tax_percent' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return array{discount_amount: float, tax_amount: float, amount: float, base: float}
     */
    public static function calculateAmounts(
        int $quantity,
        float $unitPrice,
        float $discountPercent = 0,
        float $taxPercent = 0,
    ): array {
        $base = round($quantity * $unitPrice, 2);
        $discountAmount = round($base * (max(0, min(100, $discountPercent)) / 100), 2);
        $taxable = round($base - $discountAmount, 2);
        $taxAmount = round($taxable * (max(0, min(100, $taxPercent)) / 100), 2);
        $amount = round($taxable + $taxAmount, 2);

        return [
            'base' => $base,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'amount' => $amount,
        ];
    }
}

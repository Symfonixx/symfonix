<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Product\Models\Product;
use Modules\Services\Models\Service;
use Modules\Tax\Models\TaxRate;

class InvoiceLine extends Model
{
    protected $fillable = [
        'invoice_id',
        'service_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'tax_rate_id',
        'tax_percent',
        'tax_amount',
        'amount',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'tax_percent' => 'decimal:4',
            'tax_amount' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }
}

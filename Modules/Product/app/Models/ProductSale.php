<?php

namespace Modules\Product\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Deal;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\JournalEntry;
use Modules\Tax\Models\TaxRate;

class ProductSale extends Model
{
    protected $fillable = [
        'product_id',
        'company_id',
        'invoice_id',
        'deal_id',
        'user_id',
        'quantity',
        'unit_price',
        'tax_rate_id',
        'tax_amount',
        'total_amount',
        'currency',
        'notes',
        'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'sold_at' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(JournalEntry::class, 'reference');
    }
}

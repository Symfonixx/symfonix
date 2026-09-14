<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Subscription;
use Modules\Finance\Enums\InvoiceStatus;
use Modules\Product\Models\ProductSale;
use Modules\Project\Models\Project;

class Invoice extends Model
{
    public const STATUS_DRAFT = InvoiceStatus::DRAFT->value;

    public const STATUS_SENT = InvoiceStatus::SENT->value;

    public const STATUS_PAID = InvoiceStatus::PAID->value;

    public const STATUS_OVERDUE = InvoiceStatus::OVERDUE->value;

    public const STATUS_VOID = InvoiceStatus::VOID->value;

    public const OPEN_STATUSES = [
        self::STATUS_SENT,
        self::STATUS_OVERDUE,
    ];

    protected $fillable = [
        'invoice_number',
        'company_id',
        'subscription_id',
        'deal_id',
        'project_id',
        'status',
        'subtotal',
        'tax_amount',
        'total',
        'currency',
        'issued_at',
        'due_at',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'issued_at' => 'date',
            'due_at' => 'date',
            'paid_at' => 'date',
        ];
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', self::OPEN_STATUSES);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class)->orderBy('sort_order');
    }

    public function productSales(): HasMany
    {
        return $this->hasMany(ProductSale::class);
    }

    public function journalEntries(): MorphMany
    {
        return $this->morphMany(JournalEntry::class, 'reference');
    }
}

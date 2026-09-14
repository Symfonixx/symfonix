<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JournalEntry extends Model
{
    public const FLOW_REVENUE = 'revenue';

    public const FLOW_EXPENSE = 'expense';

    protected $fillable = [
        'flow',
        'amount',
        'currency',
        'exchange_rate',
        'base_amount',
        'reference_type',
        'reference_id',
        'description',
        'transaction_date',
        'tax_rate_id',
        'tax_amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'exchange_rate' => 'decimal:8',
            'base_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id');
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('transaction_date', today());
    }

    public function scopeRevenue(Builder $query): Builder
    {
        return $query->where('flow', self::FLOW_REVENUE);
    }

    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('flow', self::FLOW_EXPENSE);
    }
}

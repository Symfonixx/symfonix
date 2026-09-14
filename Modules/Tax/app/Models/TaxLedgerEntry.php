<?php

namespace Modules\Tax\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Subscription;
use Modules\Project\Models\Project;

class TaxLedgerEntry extends Model
{
    public const DIRECTION_OUTPUT = 'output';

    public const DIRECTION_INPUT = 'input';

    protected $fillable = [
        'tax_rate_id',
        'direction',
        'amount',
        'currency',
        'base_amount',
        'transaction_date',
        'source_type',
        'source_id',
        'company_id',
        'project_id',
        'subscription_id',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'base_amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function scopeOutput(Builder $query): Builder
    {
        return $query->where('direction', self::DIRECTION_OUTPUT);
    }

    public function scopeInput(Builder $query): Builder
    {
        return $query->where('direction', self::DIRECTION_INPUT);
    }

    public function scopeBetweenDates(Builder $query, string $from, string $to): Builder
    {
        return $query->whereDate('transaction_date', '>=', $from)
            ->whereDate('transaction_date', '<=', $to);
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}

<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\CRM\Models\Deal;
use Modules\User\Models\Employee;

class Commission extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'deal_id',
        'employee_id',
        'commission_percentage',
        'commission_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'commission_percentage' => 'decimal:2',
            'commission_amount' => 'decimal:2',
        ];
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function journalEntry(): MorphOne
    {
        return $this->morphOne(JournalEntry::class, 'reference');
    }

    /** @deprecated Use journalEntry() */
    public function transaction(): MorphOne
    {
        return $this->journalEntry();
    }
}


<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\User\Models\Employee;

class Salary extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'employee_id',
        'base_salary',
        'period',
        'paid_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'base_salary' => 'decimal:2',
            'period' => 'date',
            'paid_at' => 'date',
        ];
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


<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalLine extends Model
{
    public const SIDE_DEBIT = 'debit';

    public const SIDE_CREDIT = 'credit';

    public const ACCOUNT_CASH = 'cash';

    public const ACCOUNT_REVENUE = 'revenue';

    public const ACCOUNT_EXPENSE = 'expense';

    protected $fillable = [
        'journal_entry_id',
        'side',
        'account',
        'expense_category_id',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function scopeDebit(Builder $query): Builder
    {
        return $query->where('side', self::SIDE_DEBIT);
    }

    public function scopeCredit(Builder $query): Builder
    {
        return $query->where('side', self::SIDE_CREDIT);
    }

    public function scopeRevenueCredits(Builder $query): Builder
    {
        return $query->where('account', self::ACCOUNT_REVENUE)
            ->where('side', self::SIDE_CREDIT);
    }

    public function scopeExpenseDebits(Builder $query): Builder
    {
        return $query->where('account', self::ACCOUNT_EXPENSE)
            ->where('side', self::SIDE_DEBIT);
    }
}

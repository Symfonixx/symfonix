<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CRM\Models\Subscription;

class SubscriptionBilling extends Model
{
    protected $fillable = [
        'subscription_id',
        'billing_date',
        'invoice_id',
    ];

    protected function casts(): array
    {
        return [
            'billing_date' => 'date',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}

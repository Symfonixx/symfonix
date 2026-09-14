<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CRM\Concerns\HasCrmTimeline;
use Modules\CRM\Enums\SubscriptionBillingCycle;
use Modules\CRM\Enums\SubscriptionStatus;
use Modules\CRM\Filters\Subscription\SubscriptionFilter;
use Modules\Finance\Models\Invoice;
use Modules\Services\Models\Service;

class Subscription extends Model
{
    use HasCrmTimeline, SoftDeletes;

    public const STATUS_ACTIVE = SubscriptionStatus::ACTIVE->value;

    public const STATUS_TRIAL = SubscriptionStatus::TRIAL->value;

    public const STATUS_PAUSED = SubscriptionStatus::PAUSED->value;

    public const STATUS_CANCELLED = SubscriptionStatus::CANCELLED->value;

    public const STATUS_EXPIRED = SubscriptionStatus::EXPIRED->value;

    public const BILLING_MONTHLY = SubscriptionBillingCycle::MONTHLY->value;

    public const BILLING_QUARTERLY = SubscriptionBillingCycle::QUARTERLY->value;

    public const BILLING_YEARLY = SubscriptionBillingCycle::YEARLY->value;

    public const BILLING_ONE_TIME = SubscriptionBillingCycle::ONE_TIME->value;

    public const BILLING_CYCLES = [
        self::BILLING_MONTHLY,
        self::BILLING_QUARTERLY,
        self::BILLING_YEARLY,
        self::BILLING_ONE_TIME,
    ];

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_TRIAL,
        self::STATUS_PAUSED,
        self::STATUS_CANCELLED,
        self::STATUS_EXPIRED,
    ];

    protected $fillable = [
        'company_id',
        'service_id',
        'name',
        'status',
        'billing_cycle',
        'amount',
        'currency',
        'starts_at',
        'ends_at',
        'renewal_at',
        'auto_renew',
        'cancelled_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'starts_at' => 'date',
            'ends_at' => 'date',
            'renewal_at' => 'date',
            'auto_renew' => 'boolean',
            'cancelled_at' => 'datetime',
        ];
    }

    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        return (new SubscriptionFilter)->apply($query, $filters);
    }

    public function scopeRenewingSoon(Builder $query, int $days = 30): Builder
    {
        return $query
            ->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_TRIAL])
            ->whereNotNull('renewal_at')
            ->whereBetween('renewal_at', [now()->toDateString(), now()->addDays($days)->toDateString()]);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'subscription_service')
            ->withTimestamps();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->latest('issued_at');
    }
}

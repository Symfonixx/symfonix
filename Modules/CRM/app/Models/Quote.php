<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\CRM\Enums\QuoteStatus;
use Modules\Project\Models\Project;

class Quote extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = QuoteStatus::DRAFT->value;

    public const STATUS_SENT = QuoteStatus::SENT->value;

    public const STATUS_ACCEPTED = QuoteStatus::ACCEPTED->value;

    public const STATUS_REJECTED = QuoteStatus::REJECTED->value;

    public const STATUS_EXPIRED = QuoteStatus::EXPIRED->value;

    public const STATUS_VOID = QuoteStatus::VOID->value;

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_SENT,
        self::STATUS_ACCEPTED,
        self::STATUS_REJECTED,
        self::STATUS_EXPIRED,
        self::STATUS_VOID,
    ];

    protected $fillable = [
        'uuid',
        'quote_number',
        'company_id',
        'deal_id',
        'project_id',
        'status',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total',
        'currency',
        'terms',
        'notes',
        'issued_at',
        'expires_at',
        'responded_at',
        'responder_name',
        'responder_email',
        'responder_ip',
        'response_note',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'issued_at' => 'date',
            'expires_at' => 'date',
            'responded_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Quote $quote) {
            if (empty($quote->uuid)) {
                $quote->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['company_id'])) {
            $query->where('company_id', (int) $filters['company_id']);
        }

        if (! empty($filters['deal_id'])) {
            $query->where('deal_id', (int) $filters['deal_id']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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
        return $this->hasMany(QuoteLine::class)->orderBy('sort_order');
    }

    public function isRespondable(): bool
    {
        return $this->status === self::STATUS_SENT && ! $this->isPastExpiry();
    }

    public function isPastExpiry(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function publicUrl(): string
    {
        return route('quotes.public', $this->uuid);
    }
}

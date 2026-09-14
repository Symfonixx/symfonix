<?php

namespace Modules\Tax\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxRate extends Model
{
    public const TYPE_INCLUSIVE = 'inclusive';

    public const TYPE_EXCLUSIVE = 'exclusive';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'name',
        'percentage',
        'type',
        'region_code',
        'status',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'percentage' => 'decimal:4',
            'is_default' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeForRegion(Builder $query, ?string $regionCode): Builder
    {
        if ($regionCode === null || $regionCode === '') {
            return $query;
        }

        return $query->where(function (Builder $inner) use ($regionCode) {
            $inner->where('region_code', $regionCode)
                ->orWhereNull('region_code');
        });
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(TaxLedgerEntry::class);
    }

    public function isInclusive(): bool
    {
        return $this->type === self::TYPE_INCLUSIVE;
    }

    public function isExclusive(): bool
    {
        return $this->type === self::TYPE_EXCLUSIVE;
    }

    public static function resolveDefault(?string $regionCode = null): ?self
    {
        $query = self::query()->active();

        if ($regionCode) {
            $regional = (clone $query)
                ->where('region_code', $regionCode)
                ->where('is_default', true)
                ->first();

            if ($regional) {
                return $regional;
            }
        }

        return $query->where('is_default', true)->first()
            ?? $query->orderBy('name')->first();
    }
}

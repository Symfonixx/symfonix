<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\CRM\Models\CrmSalesTarget;

class Employee extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'img',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    protected $appends = ['avatar'];

    public function scopeActive(Builder $query): void
    {
        $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeAssignable(Builder $query): void
    {
        $query->active()->orderBy('name');
    }

    public function crmSalesTarget(): HasOne
    {
        return $this->hasOne(CrmSalesTarget::class);
    }

    public function getAvatarAttribute(): string
    {
        if (! empty($this->attributes['img'])) {
            return asset('storage/'.$this->attributes['img']);
        }

        return asset('images/avatar.png');
    }
}

<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\User\Database\Factories\JobPositionFactory;

class JobPosition extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'title',
        'department',
        'description',
        'requirements',
        'status',
        'posted_at',
    ];

    protected function casts(): array
    {
        return [
            'posted_at' => 'date',
        ];
    }

    protected static function newFactory(): JobPositionFactory
    {
        return JobPositionFactory::new();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_ACTIVE)
            ->whereDate('posted_at', '<=', today());
    }
}

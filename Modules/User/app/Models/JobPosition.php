<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Modules\User\Database\Factories\JobPositionFactory;

class JobPosition extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_CLOSED = 'closed';

    public const EMPLOYMENT_FULL_TIME = 'full_time';
    public const EMPLOYMENT_PART_TIME = 'part_time';
    public const EMPLOYMENT_CONTRACT = 'contract';
    public const EMPLOYMENT_INTERNSHIP = 'internship';
    public const EMPLOYMENT_REMOTE = 'remote';

    public const EMPLOYMENT_TYPES = [
        self::EMPLOYMENT_FULL_TIME,
        self::EMPLOYMENT_PART_TIME,
        self::EMPLOYMENT_CONTRACT,
        self::EMPLOYMENT_INTERNSHIP,
        self::EMPLOYMENT_REMOTE,
    ];

    protected $fillable = [
        'title',
        'slug',
        'department',
        'location',
        'employment_type',
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

    protected static function booted(): void
    {
        static::creating(function (JobPosition $position): void {
            if (blank($position->slug)) {
                $position->slug = static::uniqueSlug($position->title);
            }
        });

        static::updating(function (JobPosition $position): void {
            if ($position->isDirty('title') && ! $position->isDirty('slug')) {
                $position->slug = static::uniqueSlug($position->title, $position->id);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
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

    private static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'job';
        $slug = $base;
        $suffix = 2;

        while (static::query()
            ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}

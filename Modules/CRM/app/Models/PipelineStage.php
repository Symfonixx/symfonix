<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Lang;

class PipelineStage extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'sort_order',
        'probability',
        'is_won',
        'is_lost',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_won' => 'boolean',
            'is_lost' => 'boolean',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Localised stage name. Falls back to the stored name when no translation exists.
     */
    public function getDisplayNameAttribute(): string
    {
        $key = 'crm::deal.stages.'.str_replace('-', '_', (string) $this->slug);

        return Lang::has($key) ? __($key) : (string) $this->name;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }
}

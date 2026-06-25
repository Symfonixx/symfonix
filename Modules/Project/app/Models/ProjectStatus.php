<?php

namespace Modules\Project\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Deal;

class ProjectStatus extends Model
{
    protected $fillable = [
        'name',
        'color_code',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Resolve the default initial status (lowest sort_order).
     */
    public static function defaultStatus(): ?self
    {
        return static::query()->orderBy('sort_order')->first();
    }
}

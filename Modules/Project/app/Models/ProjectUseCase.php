<?php

namespace Modules\Project\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Cms\Enums\CmsStatus;
use Spatie\Translatable\HasTranslations;

class ProjectUseCase extends Model
{
    use HasTranslations;

    public array $translatable = [
        'title',
        'client_name',
        'summary',
        'challenge',
        'solution',
        'results',
        'content',
    ];

    protected $fillable = [
        'project_id',
        'title',
        'slug',
        'client_name',
        'summary',
        'challenge',
        'solution',
        'results',
        'content',
        'image',
        'technologies',
        'category_tag',
        'project_url',
        'completed_year',
        'featured',
        'status',
        'sort_order',
        'visits',
    ];

    protected $appends = ['image_link'];

    protected function casts(): array
    {
        return [
            'technologies' => 'array',
            'featured' => 'boolean',
            'completed_year' => 'integer',
            'sort_order' => 'integer',
            'visits' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', CmsStatus::PUBLISHED->value);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->latest('id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getImageLinkAttribute(): string
    {
        if (! empty($this->attributes['image'])) {
            return asset('storage/'.$this->attributes['image']);
        }

        return asset('images/blank.png');
    }
}

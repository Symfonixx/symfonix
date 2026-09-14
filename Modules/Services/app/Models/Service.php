<?php

namespace Modules\Services\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Subscription;
use Modules\Services\Enums\ServiceStatus;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasTranslations;

    public $translatable = ['title', 'description', 'content', 'keywords'];

    protected $appends = ['image_link'];

    protected $fillable = ['title', 'service_category_id', 'slug', 'description', 'content', 'image', 'status', 'keywords', 'featured', 'visits'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function deals(): BelongsToMany
    {
        return $this->belongsToMany(Deal::class, 'deal_service')
            ->withPivot(['quantity', 'unit_price'])
            ->withTimestamps();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeFeatured(Builder $q): Builder
    {
        return $q->where('status', ServiceStatus::PUBLISHED->value)->where('featured', 1);
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', ServiceStatus::PUBLISHED->value);
    }

    public function getImageLinkAttribute(): string
    {
        if ($this->attributes['image']) {
            $path = asset('storage/'.$this->attributes['image']);
        } else {
            $path = asset('images/blank.png');
        }

        return $path;
    }
}

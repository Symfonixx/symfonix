<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Cms\Enums\CmsStatus;
use Spatie\Translatable\HasTranslations;

class Client extends Model
{
    use HasTranslations;

    public $translatable = ['name'];

    protected $appends = ['logo_link'];

    protected $fillable = [
        'name',
        'logo',
        'url',
        'rank',
        'status',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', CmsStatus::PUBLISHED->value);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('rank', 'asc')->orderBy('id', 'asc');
    }

    public function getLogoLinkAttribute(): string
    {
        if (! empty($this->attributes['logo'])) {
            return asset('storage/'.$this->attributes['logo']);
        }

        return asset('images/blank.png');
    }
}

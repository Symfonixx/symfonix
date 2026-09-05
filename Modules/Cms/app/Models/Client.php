<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Model;
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

    public function scopePublished($query)
    {
        $query->where('status', 'Published');
    }

    public function scopeOrdered($query)
    {
        $query->orderBy('rank', 'asc')->orderBy('id', 'asc');
    }

    public function getLogoLinkAttribute(): string
    {
        if (! empty($this->attributes['logo'])) {
            return asset('storage/'.$this->attributes['logo']);
        }

        return asset('images/blank.png');
    }
}

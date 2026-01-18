<?php

namespace Modules\Testimonial\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Testimonial extends Model
{
    use HasTranslations;

    public $translatable = ['name', 'position', 'quote'];

    protected $appends = ['avatar_link'];

    protected $fillable = [
        'name', 'position', 'url', 'avatar', 'quote', 'status',
    ];

    public function getAvatarLinkAttribute()
    {
        if ($this->attributes['avatar']) {
            $path = asset('storage/'.$this->attributes['avatar']);
        } else {
            $path = asset('images/blank.png');
        }

        return $path;
    }
}

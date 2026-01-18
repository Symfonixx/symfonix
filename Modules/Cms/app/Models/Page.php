<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasTranslations;

    public $translatable = ['title', 'description', 'content', 'keywords'];

    protected $appends = ['image_link'];

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'image',
        'status',
        'keywords',
        'featured',
        'add_to_nav',
        'add_to_footer',
        'add_to_top_bar',
        'visits',
    ];

    public function scopeFeatured($q)
    {
        $q->where('status', 'Published')->where('featured', 1);
    }

     public function scopePublished($q)
    {
        $q->where('status', 'Published');
    }

    public function getImageLinkAttribute()
    {
        if ($this->attributes['image']) {
            $path = asset('storage/'.$this->attributes['image']);
        } else {
            $path = asset('images/blank.png');
        }

        return $path;
    }
}

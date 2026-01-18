<?php

namespace Modules\Services\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasTranslations;

    public $translatable = ['title', 'description', 'content', 'keywords'];

    protected $appends = ['image_link'];

    protected $fillable = ['title', 'service_category_id' , 'slug', 'description', 'content', 'image', 'status', 'keywords', 'featured', 'visits'];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

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

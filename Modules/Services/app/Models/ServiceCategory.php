<?php

namespace Modules\Services\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ServiceCategory extends Model
{
    use HasTranslations;

    public $translatable = ['title', 'description'];

    protected $appends = ['image_link'];

    protected $fillable = ['image', 'title', 'slug', 'description', 'color_code'];

    public function services()
    {
        return $this->hasMany(Service::class, 'service_category_id');
    }

    public function getImageLinkAttribute()
    {
        if (!empty($this->attributes['image'])) {
            return asset('storage/'.$this->attributes['image']);
        }
        return asset('images/blank.png');
    }
}



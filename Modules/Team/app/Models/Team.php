<?php

namespace Modules\Team\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Team extends Model
{
    use HasTranslations;

    public $translatable = ['name', 'position'];

    protected $appends = ['avatar_link', 'resume_link'];

    protected $fillable = [
        'name', 'position', 'linked_in', 'facebook', 'github', 'behance', 'resume', 'key_skills', 'avatar', 'status',
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

    public function getResumeLinkAttribute()
    {
        if (isset($this->attributes['resume']) && $this->attributes['resume']) {
            return asset('storage/'.$this->attributes['resume']);
        }

        return null;
    }
}

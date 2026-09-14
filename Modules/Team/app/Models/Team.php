<?php

namespace Modules\Team\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Cms\Enums\CmsStatus;
use Modules\User\Models\Employee;
use Spatie\Translatable\HasTranslations;

class Team extends Model
{
    use HasTranslations;

    public $translatable = ['name', 'position'];

    protected $appends = ['avatar_link', 'resume_link'];

    protected $fillable = [
        'name',
        'position',
        'linked_in',
        'facebook',
        'github',
        'behance',
        'resume',
        'key_skills',
        'avatar',
        'status',
        'employee_id',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', CmsStatus::PUBLISHED->value);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getAvatarLinkAttribute(): string
    {
        if ($this->attributes['avatar']) {
            $path = asset('storage/'.$this->attributes['avatar']);
        } else {
            $path = asset('images/blank.png');
        }

        return $path;
    }

    public function getResumeLinkAttribute(): ?string
    {
        if (isset($this->attributes['resume']) && $this->attributes['resume']) {
            return asset('storage/'.$this->attributes['resume']);
        }

        return null;
    }
}

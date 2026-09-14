<?php

namespace Modules\Testimonial\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Cms\Enums\CmsStatus;
use Modules\Project\Models\Project;
use Spatie\Translatable\HasTranslations;

class Testimonial extends Model
{
    use HasTranslations;

    public $translatable = ['quote'];

    protected $fillable = [
        'project_id',
        'customer_id',
        'quote',
        'status',
    ];

    protected $appends = [
        'avatar_link',
        'name',
        'position',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', CmsStatus::PUBLISHED->value);
    }

    public function scopeWithDisplayRelations(Builder $query): Builder
    {
        return $query->with([
            'customer:id,name,email,img',
            'project:id,title,company_id',
            'project.company:id,name',
        ]);
    }

    public function getAvatarLinkAttribute(): string
    {
        return $this->customer?->avatar ?? asset('images/avatar.png');
    }

    public function getNameAttribute(): string
    {
        return $this->customer?->name ?? '';
    }

    public function getPositionAttribute(): string
    {
        $companyName = $this->project?->company?->name;
        $projectTitle = $this->project?->title;

        if ($companyName && $projectTitle) {
            return $companyName.' · '.$projectTitle;
        }

        return $companyName ?? $projectTitle ?? '';
    }
}

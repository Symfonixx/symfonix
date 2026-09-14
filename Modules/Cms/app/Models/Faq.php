<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Cms\Enums\CmsStatus;
use Spatie\Translatable\HasTranslations;

class Faq extends Model
{
    use HasTranslations;

    public $translatable = ['question', 'answer'];

    protected $fillable = ['question', 'answer', 'rank', 'status'];

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', CmsStatus::PUBLISHED->value);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('rank', 'asc')->orderBy('id', 'asc');
    }
}

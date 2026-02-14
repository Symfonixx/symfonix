<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Faq extends Model
{
    use HasTranslations;

    public $translatable = ['question', 'answer'];

    protected $fillable = ['question', 'answer', 'rank', 'status'];

    public function scopePublished($q)
    {
        $q->where('status', 'Published');
    }

    public function scopeOrdered($q)
    {
        $q->orderBy('rank', 'asc')->orderBy('id', 'asc');
    }
}

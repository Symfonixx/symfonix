<?php

namespace Modules\Base\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Branch extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'city', 'address', 'phone'];

    public array $translatable = ['name', 'city', 'address'];
}




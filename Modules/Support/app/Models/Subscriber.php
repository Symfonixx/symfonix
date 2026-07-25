<?php

namespace Modules\Support\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = ['email', 'ip_address', 'lang', 'blocked'];

    protected function casts(): array
    {
        return [
            'blocked' => 'boolean',
        ];
    }
}

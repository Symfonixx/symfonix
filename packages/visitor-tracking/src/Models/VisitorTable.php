<?php

namespace MonishRoy\VisitorTracking\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorTable extends Model
{
    protected $table = 'visitors';

    protected $fillable = [
        'ip',
        'country',
        'region',
        'city',
        'device',
        'os',
        'browser',
        'page_title',
        'url'
    ];
}

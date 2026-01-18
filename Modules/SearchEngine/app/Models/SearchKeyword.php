<?php

namespace Modules\SearchEngine\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchKeyword extends Model
{
    use HasFactory;

    protected $table = 'search_keywords';

    protected $fillable = [
        'keyword',
        'count',
    ];
}


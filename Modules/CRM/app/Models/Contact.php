<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CRM\Concerns\HasCrmTimeline;
use Modules\CRM\Filters\Contact\ContactFilter;

class Contact extends Model
{
    use HasCrmTimeline, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'job_title',
        'notes',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        return (new ContactFilter)->apply($query, $filters);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

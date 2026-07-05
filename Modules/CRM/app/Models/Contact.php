<?php

namespace Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CRM\Concerns\HasCrmTimeline;
use Modules\CRM\Filters\Contact\ContactFilter;

class Contact extends Model
{
    use HasCrmTimeline, SoftDeletes;

    /**
     * Available contact sources (shared with leads for consistency).
     */
    public const SOURCES = Lead::SOURCES;

    protected $fillable = [
        'company_id',
        'user_id',
        'name',
        'email',
        'phone',
        'phone2',
        'source',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

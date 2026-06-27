<?php

namespace Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CRM\Concerns\HasCrmTimeline;
use Modules\CRM\Filters\Company\CompanyFilter;

class Company extends Model
{
    use HasCrmTimeline, SoftDeletes;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_DISABLED = 'disabled';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'country',
        'city',
        'address',
        'notes',
        'status',
    ];

    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        return (new CompanyFilter)->apply($query, $filters);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contactForms(): HasMany
    {
        return $this->hasMany(ContactForm::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}

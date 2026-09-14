<?php

namespace Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CRM\Concerns\HasCrmTimeline;
use Modules\CRM\Enums\CompanyActivityType;
use Modules\CRM\Enums\CompanyStatus;
use Modules\CRM\Filters\Company\CompanyFilter;
use Modules\Project\Models\Project;

class Company extends Model
{
    use HasCrmTimeline, SoftDeletes;

    public const STATUS_ACTIVE = CompanyStatus::ACTIVE->value;

    public const STATUS_DISABLED = CompanyStatus::DISABLED->value;

    public const ACTIVITY_TECHNOLOGY = CompanyActivityType::TECHNOLOGY->value;

    public const ACTIVITY_RETAIL = CompanyActivityType::RETAIL->value;

    public const ACTIVITY_HEALTHCARE = CompanyActivityType::HEALTHCARE->value;

    public const ACTIVITY_FINANCE = CompanyActivityType::FINANCE->value;

    public const ACTIVITY_EDUCATION = CompanyActivityType::EDUCATION->value;

    public const ACTIVITY_MANUFACTURING = CompanyActivityType::MANUFACTURING->value;

    public const ACTIVITY_CONSULTING = CompanyActivityType::CONSULTING->value;

    public const ACTIVITY_OTHER = CompanyActivityType::OTHER->value;

    public const ACTIVITY_TYPES = [
        self::ACTIVITY_TECHNOLOGY,
        self::ACTIVITY_RETAIL,
        self::ACTIVITY_HEALTHCARE,
        self::ACTIVITY_FINANCE,
        self::ACTIVITY_EDUCATION,
        self::ACTIVITY_MANUFACTURING,
        self::ACTIVITY_CONSULTING,
        self::ACTIVITY_OTHER,
    ];

    protected $fillable = [
        'user_id',
        'name',
        'activity_type',
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

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class)->latest();
    }
}

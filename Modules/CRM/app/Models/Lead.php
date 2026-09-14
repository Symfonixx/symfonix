<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\CRM\Concerns\HasCrmTimeline;
use Modules\CRM\Enums\LeadSource;
use Modules\CRM\Enums\LeadStatus;
use Modules\CRM\Filters\Lead\LeadFilter;
use Modules\Services\Models\Service;
use Modules\User\Models\Employee;

class Lead extends Model
{
    use HasCrmTimeline;

    public const SOURCE_ORGANIC_SEARCH = LeadSource::ORGANIC_SEARCH->value;

    public const SOURCE_DIRECT = LeadSource::DIRECT->value;

    public const SOURCE_SOCIAL_MEDIA = LeadSource::SOCIAL_MEDIA->value;

    public const SOURCE_REFERRAL = LeadSource::REFERRAL->value;

    public const SOURCE_PAID_ADS = LeadSource::PAID_ADS->value;

    public const SOURCE_WEBSITE = LeadSource::WEBSITE->value;

    public const SOURCE_SALES = LeadSource::SALES->value;

    public const SOURCE_MANUAL = LeadSource::MANUAL->value;

    public const SOURCES = [
        self::SOURCE_ORGANIC_SEARCH,
        self::SOURCE_DIRECT,
        self::SOURCE_SOCIAL_MEDIA,
        self::SOURCE_REFERRAL,
        self::SOURCE_PAID_ADS,
        self::SOURCE_WEBSITE,
        self::SOURCE_SALES,
        self::SOURCE_MANUAL,
    ];

    public const STATUS_NEW = LeadStatus::NEW->value;

    public const STATUS_CONTACTED = LeadStatus::CONTACTED->value;

    public const STATUS_QUALIFIED = LeadStatus::QUALIFIED->value;

    public const STATUS_UNQUALIFIED = LeadStatus::UNQUALIFIED->value;

    public const STATUS_CONVERTED = LeadStatus::CONVERTED->value;

    public const STATUS_LOST = LeadStatus::LOST->value;

    public const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_CONTACTED,
        self::STATUS_QUALIFIED,
        self::STATUS_UNQUALIFIED,
        self::STATUS_CONVERTED,
        self::STATUS_LOST,
    ];

    public static function statusBadgeColor(?string $status): string
    {
        return match ($status) {
            self::STATUS_NEW => 'primary',
            self::STATUS_CONTACTED => 'info',
            self::STATUS_QUALIFIED => 'success',
            self::STATUS_CONVERTED => 'dark',
            self::STATUS_UNQUALIFIED, self::STATUS_LOST => 'danger',
            default => 'secondary',
        };
    }

    public static function sourceBadgeColor(?string $source): string
    {
        return match ($source) {
            self::SOURCE_ORGANIC_SEARCH => 'success',
            self::SOURCE_DIRECT, self::SOURCE_WEBSITE => 'primary',
            self::SOURCE_SOCIAL_MEDIA => 'info',
            self::SOURCE_REFERRAL, self::SOURCE_MANUAL => 'warning',
            self::SOURCE_PAID_ADS => 'danger',
            self::SOURCE_SALES => 'dark',
            default => 'secondary',
        };
    }

    protected $fillable = [
        'name',
        'email',
        'phone',
        'job_title',
        'company_name',
        'company_id',
        'city',
        'country',
        'website',
        'industry',
        'assigned_to',
        'deal_id',
        'converted_at',
        'source',
        'status',
        'project_budget',
        'service_interest',
        'service_id',
        'service_matches',
        'problem_statement',
        'chat_transcript',
        'meta',
        'custom_fields',
        'attachments',
        'botman_user_id',
        'botman_driver',
        'locale',
        'ip_address',
        'blocked',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'lead_service')
            ->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(LeadTag::class, 'lead_lead_tag')
            ->withTimestamps();
    }

    /** Service ids from the pivot, falling back to the legacy single column. */
    public function serviceIds(): array
    {
        $this->loadMissing('services');

        $ids = $this->services->pluck('id')->map(fn ($id) => (int) $id)->all();

        if ($ids === [] && $this->service_id) {
            $ids = [(int) $this->service_id];
        }

        return $ids;
    }

    public function tagIds(): array
    {
        $this->loadMissing('tags');

        return $this->tags->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        return (new LeadFilter)->apply($query, $filters);
    }

    protected $casts = [
        'service_matches' => 'array',
        'chat_transcript' => 'array',
        'meta' => 'array',
        'custom_fields' => 'array',
        'attachments' => 'array',
        'blocked' => 'boolean',
        'converted_at' => 'datetime',
    ];

    public function customFieldValue(string $key, mixed $default = null): mixed
    {
        return ($this->custom_fields ?? [])[$key] ?? $default;
    }
}

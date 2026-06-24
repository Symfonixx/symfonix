<?php

namespace Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CRM\Concerns\HasCrmTimeline;
use Modules\Services\Models\Service;

class Lead extends Model
{
    use HasCrmTimeline;

    public const SOURCE_ORGANIC_SEARCH = 'organic_search';

    public const SOURCE_DIRECT = 'direct';

    public const SOURCE_SOCIAL_MEDIA = 'social_media';

    public const SOURCE_REFERRAL = 'referral';

    public const SOURCE_PAID_ADS = 'paid_ads';

    public const SOURCE_WEBSITE = 'website';

    public const SOURCE_SALES = 'sales';

    public const SOURCE_MANUAL = 'manual';

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
        'company_name',
        'company_id',
        'assigned_to',
        'deal_id',
        'converted_at',
        'source',
        'project_budget',
        'service_interest',
        'service_id',
        'service_matches',
        'problem_statement',
        'chat_transcript',
        'meta',
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

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    protected $casts = [
        'service_matches' => 'array',
        'chat_transcript' => 'array',
        'meta' => 'array',
        'blocked' => 'boolean',
        'converted_at' => 'datetime',
    ];
}

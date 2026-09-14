<?php

namespace Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CRM\Concerns\HasCampaignStatus;
use Modules\CRM\Enums\MarketingCampaignStatus;

class MarketingCampaign extends Model
{
    use HasCampaignStatus;

    public const STATUS_PENDING = MarketingCampaignStatus::PENDING->value;

    public const STATUS_FINISHED = MarketingCampaignStatus::FINISHED->value;

    public const STATUS_FAILED = MarketingCampaignStatus::FAILED->value;

    protected $fillable = [
        'user_id',
        'subject',
        'body',
        'recipients_count',
        'status',
        'recipient_sources',
    ];

    protected function casts(): array
    {
        return [
            'recipient_sources' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsFinished(): void
    {
        $this->update(['status' => self::STATUS_FINISHED]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => self::STATUS_FAILED]);
    }
}

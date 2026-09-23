<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CRM\Enums\MarketingEmailStatus;

class MarketingEmailLog extends Model
{
    protected $table = 'marketing_email_logs';

    public const STATUS_PENDING = MarketingEmailStatus::PENDING->value;

    public const STATUS_QUEUED = MarketingEmailStatus::QUEUED->value;

    public const STATUS_FAILED = MarketingEmailStatus::FAILED->value;

    protected $fillable = [
        'marketing_campaign_id',
        'email',
        'status',
        'error_message',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MarketingCampaign::class, 'marketing_campaign_id');
    }

    public function markAsQueued(): void
    {
        $this->update([
            'status' => self::STATUS_QUEUED,
            'sent_at' => now(),
            'error_message' => null,
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
        ]);
    }
}

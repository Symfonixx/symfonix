<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CRM\Enums\WhatsAppMessageStatus;

class WhatsAppMessageLog extends Model
{
    protected $table = 'whatsapp_message_logs';

    public const STATUS_PENDING = WhatsAppMessageStatus::PENDING->value;

    public const STATUS_SENT = WhatsAppMessageStatus::SENT->value;

    public const STATUS_FAILED = WhatsAppMessageStatus::FAILED->value;

    protected $fillable = [
        'whatsapp_campaign_id',
        'phone',
        'recipient_type',
        'recipient_id',
        'status',
        'meta_message_id',
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
        return $this->belongsTo(WhatsAppCampaign::class, 'whatsapp_campaign_id');
    }

    public function markAsSent(?string $metaMessageId = null): void
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'meta_message_id' => $metaMessageId,
            'sent_at' => now(),
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

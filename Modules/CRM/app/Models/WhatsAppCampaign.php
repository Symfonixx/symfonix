<?php

namespace Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CRM\Concerns\HasCampaignStatus;
use Modules\CRM\Enums\WhatsAppCampaignStatus;

class WhatsAppCampaign extends Model
{
    use HasCampaignStatus;

    protected $table = 'whatsapp_campaigns';

    public const STATUS_PENDING = WhatsAppCampaignStatus::PENDING->value;

    public const STATUS_SENDING = WhatsAppCampaignStatus::SENDING->value;

    public const STATUS_FINISHED = WhatsAppCampaignStatus::FINISHED->value;

    public const STATUS_FAILED = WhatsAppCampaignStatus::FAILED->value;

    protected $fillable = [
        'user_id',
        'whatsapp_template_id',
        'template_parameters',
        'rendered_preview',
        'recipients_count',
        'status',
        'recipient_sources',
    ];

    protected function casts(): array
    {
        return [
            'template_parameters' => 'array',
            'recipient_sources' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(WhatsAppTemplate::class, 'whatsapp_template_id');
    }

    public function messageLogs(): HasMany
    {
        return $this->hasMany(WhatsAppMessageLog::class);
    }

    public function markAsSending(): void
    {
        $this->update(['status' => self::STATUS_SENDING]);
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

<?php

namespace Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CRM\Enums\WhatsAppHeaderType;
use Modules\CRM\Enums\WhatsAppTemplateStatus;

class WhatsAppTemplate extends Model
{
    protected $table = 'whatsapp_templates';

    public const STATUS_APPROVED = WhatsAppTemplateStatus::APPROVED->value;

    public const STATUS_PENDING = WhatsAppTemplateStatus::PENDING->value;

    public const STATUS_REJECTED = WhatsAppTemplateStatus::REJECTED->value;

    public const STATUS_DRAFT = WhatsAppTemplateStatus::DRAFT->value;

    public const HEADER_NONE = WhatsAppHeaderType::NONE->value;

    public const HEADER_TEXT = WhatsAppHeaderType::TEXT->value;

    public const HEADER_IMAGE = WhatsAppHeaderType::IMAGE->value;

    public const HEADER_VIDEO = WhatsAppHeaderType::VIDEO->value;

    public const HEADER_DOCUMENT = WhatsAppHeaderType::DOCUMENT->value;

    protected $fillable = [
        'name',
        'language',
        'category',
        'status',
        'header_type',
        'header_content',
        'body',
        'footer',
        'buttons',
        'meta_template_id',
        'is_active',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'buttons' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(WhatsAppCampaign::class);
    }

    public function isSendable(): bool
    {
        return $this->is_active && $this->status === self::STATUS_APPROVED;
    }

    public function displayName(): string
    {
        return $this->name.' ('.$this->language.')';
    }

    public function bodyVariableCount(): int
    {
        preg_match_all('/\{\{(\d+)\}\}/', $this->body, $matches);

        return count(array_unique($matches[1] ?? []));
    }
}

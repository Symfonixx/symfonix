<?php

namespace Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingGroup extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'goal',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function emailCampaigns(): HasMany
    {
        return $this->hasMany(MarketingCampaign::class, 'marketing_group_id');
    }

    public function whatsappCampaigns(): HasMany
    {
        return $this->hasMany(WhatsAppCampaign::class, 'marketing_group_id');
    }

    /**
     * @return list<string>
     */
    public function missingKeys(): array
    {
        $missing = [];

        if (trim((string) $this->title) === '') {
            $missing[] = 'title';
        }

        if (trim((string) $this->goal) === '') {
            $missing[] = 'goal';
        }

        $emailCount = (int) ($this->email_campaigns_count ?? $this->emailCampaigns()->count());
        $whatsappCount = (int) ($this->whatsapp_campaigns_count ?? $this->whatsappCampaigns()->count());

        if ($emailCount === 0 && $whatsappCount === 0) {
            $missing[] = 'sends';
        } else {
            if ($emailCount === 0) {
                $missing[] = 'email';
            }

            if ($whatsappCount === 0) {
                $missing[] = 'whatsapp';
            }
        }

        return $missing;
    }

    public function isIncomplete(): bool
    {
        return array_intersect($this->missingKeys(), ['title', 'goal', 'sends']) !== [];
    }

    /**
     * @return list<string>
     */
    public function missingLabels(): array
    {
        return array_map(
            fn (string $key) => __('crm::marketing.missing.'.$key),
            $this->missingKeys(),
        );
    }
}

<?php

namespace Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingCampaign extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'body',
        'recipients_count',
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
}

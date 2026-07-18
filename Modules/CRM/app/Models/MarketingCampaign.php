<?php

namespace Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingCampaign extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_FINISHED = 'finished';

    public const STATUS_FAILED = 'failed';

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

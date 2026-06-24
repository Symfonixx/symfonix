<?php

namespace Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CrmActivity extends Model
{
    public const TYPE_NOTE = 'note';

    public const TYPE_CALL = 'call';

    public const TYPE_MEETING = 'meeting';

    public const TYPE_TASK = 'task';

    public const TYPE_EMAIL = 'email';

    public const TYPES = [
        self::TYPE_NOTE,
        self::TYPE_CALL,
        self::TYPE_MEETING,
        self::TYPE_TASK,
        self::TYPE_EMAIL,
    ];

    protected $table = 'crm_activities';

    protected $fillable = [
        'subject_type',
        'subject_id',
        'type',
        'title',
        'body',
        'scheduled_at',
        'completed_at',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\CRM\Enums\CrmActivityType;

class CrmActivity extends Model
{
    public const TYPE_NOTE = CrmActivityType::NOTE->value;

    public const TYPE_CALL = CrmActivityType::CALL->value;

    public const TYPE_MEETING = CrmActivityType::MEETING->value;

    public const TYPE_TASK = CrmActivityType::TASK->value;

    public const TYPE_EMAIL = CrmActivityType::EMAIL->value;

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

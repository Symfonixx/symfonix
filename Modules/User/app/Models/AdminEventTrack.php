<?php

namespace Modules\User\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AdminEventTrack extends Model
{
    public const UPDATED_AT = null;

    public const EVENT_VIEWED = 'viewed';

    public const EVENT_CREATED = 'created';

    public const EVENT_UPDATED = 'updated';

    public const EVENT_DELETED = 'deleted';

    public const EVENT_ACTION = 'action';

    protected $table = 'admin_event_tracks';

    protected $fillable = [
        'user_id',
        'event',
        'route_name',
        'method',
        'description',
        'subject_type',
        'subject_id',
        'subject_label',
        'metadata',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}

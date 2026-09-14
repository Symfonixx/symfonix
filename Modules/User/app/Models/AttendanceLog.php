<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Enums\PunchState;
use Modules\User\Enums\VerifyMode;

class AttendanceLog extends Model
{
    protected $fillable = [
        'employee_id',
        'device_user_id',
        'device_uid',
        'punch_state',
        'verify_mode',
        'recorded_at',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'device_uid' => 'integer',
            'punch_state' => 'integer',
            'verify_mode' => 'integer',
            'recorded_at' => 'datetime',
            'synced_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function punchStateLabel(): string
    {
        return PunchState::tryFrom((int) $this->punch_state)?->label() ?? 'undefined';
    }

    public function verifyModeLabel(): ?string
    {
        if ($this->verify_mode === null) {
            return null;
        }

        return VerifyMode::tryFrom((int) $this->verify_mode)?->name ?? 'Unknown';
    }
}

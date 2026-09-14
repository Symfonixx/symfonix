<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ZkTeco\Enums\PunchState;
use ZkTeco\Enums\VerifyMode;

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
        $state = PunchState::tryFrom((int) $this->punch_state);

        return match ($state) {
            PunchState::CheckIn => 'check_in',
            PunchState::CheckOut => 'check_out',
            PunchState::BreakOut => 'break_out',
            PunchState::BreakIn => 'break_in',
            PunchState::OvertimeIn => 'overtime_in',
            PunchState::OvertimeOut => 'overtime_out',
            default => 'undefined',
        };
    }

    public function verifyModeLabel(): ?string
    {
        if ($this->verify_mode === null) {
            return null;
        }

        return match ((int) $this->verify_mode) {
            1 => VerifyMode::Fingerprint->name,
            3 => VerifyMode::Password->name,
            4 => VerifyMode::Card->name,
            15 => VerifyMode::Face->name,
            0 => VerifyMode::Other->name,
            default => 'Unknown',
        };
    }
}

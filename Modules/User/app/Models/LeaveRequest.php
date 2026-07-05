<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    public const TYPE_ANNUAL = 'annual';

    public const TYPE_SICK = 'sick';

    public const TYPE_UNPAID = 'unpaid';

    public const TYPE_EMERGENCY = 'emergency';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'employee_id',
        'type',
        'start_date',
        'end_date',
        'status',
        'reason',
        'manager_note',
    ];

    protected $appends = ['days_count'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'type' => 'string',
            'status' => 'string',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getDaysCountAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * @return array<string, string>
     */
    public static function types(): array
    {
        return [
            self::TYPE_ANNUAL => 'Annual Leave',
            self::TYPE_SICK => 'Sick Leave',
            self::TYPE_UNPAID => 'Unpaid Leave',
            self::TYPE_EMERGENCY => 'Emergency Leave',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }
}

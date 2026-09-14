<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Database\Factories\JobApplicationFactory;

class JobApplication extends Model
{
    use HasFactory;

    public const STATUS_APPLIED = 'applied';

    public const STATUS_SCREENING = 'screening';

    public const STATUS_INTERVIEW = 'interview';

    public const STATUS_OFFERED = 'offered';

    public const STATUS_HIRED = 'hired';

    public const STATUS_REJECTED = 'rejected';

    public const STATUSES = [
        self::STATUS_APPLIED,
        self::STATUS_SCREENING,
        self::STATUS_INTERVIEW,
        self::STATUS_OFFERED,
        self::STATUS_HIRED,
        self::STATUS_REJECTED,
    ];

    protected $fillable = [
        'candidate_id',
        'job_position_id',
        'employee_id',
        'status',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    protected static function newFactory(): JobApplicationFactory
    {
        return JobApplicationFactory::new();
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class, 'job_position_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function isHired(): bool
    {
        return $this->status === self::STATUS_HIRED || $this->employee_id !== null;
    }
}

<?php

namespace Modules\User\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\CRM\Models\CrmSalesTarget;
use Modules\Finance\Models\Salary;
use Modules\Project\Models\Project;
use Modules\Project\Models\ProjectEmployee;
use Modules\Team\Models\Team;
use Modules\User\Database\Factories\EmployeeFactory;
use Modules\User\Enums\EmployeeStatus;

class Employee extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = EmployeeStatus::ACTIVE->value;

    public const STATUS_INACTIVE = EmployeeStatus::INACTIVE->value;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'position',
        'resume',
        'img',
        'status',
        'user_id',
        'fingerprint_device_uid',
        'fingerprint_enrolled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'fingerprint_device_uid' => 'integer',
            'fingerprint_enrolled_at' => 'datetime',
        ];
    }

    protected $appends = ['avatar'];

    public function scopeActive(Builder $query): void
    {
        $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeAssignable(Builder $query): void
    {
        $query->active()->orderBy('name');
    }

    protected static function newFactory(): EmployeeFactory
    {
        return EmployeeFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function team(): HasOne
    {
        return $this->hasOne(Team::class);
    }

    public function isOnWebsiteTeam(): bool
    {
        if ($this->relationLoaded('team')) {
            return $this->team !== null;
        }

        return $this->team()->exists();
    }

    public function crmSalesTarget(): HasOne
    {
        return $this->hasOne(CrmSalesTarget::class);
    }

    public function isAdminAccount(): bool
    {
        return $this->user_id !== null;
    }

    public function adminAccount(): ?User
    {
        if ($this->relationLoaded('user')) {
            return $this->user;
        }

        if ($this->user_id) {
            return $this->user;
        }

        return null;
    }

    public function resumeUrl(): ?string
    {
        if (empty($this->resume)) {
            return null;
        }

        return asset('storage/'.$this->resume);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class)->latest('recorded_at');
    }

    public function fingerprintUserId(): string
    {
        return (string) $this->id;
    }

    public function isFingerprintEnrolled(): bool
    {
        return $this->fingerprint_enrolled_at !== null;
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class)->latest('period');
    }

    public function projectAssignments(): HasMany
    {
        return $this->hasMany(ProjectEmployee::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_employees')
            ->withPivot(['id', 'role', 'started_at', 'ended_at', 'notes'])
            ->withTimestamps();
    }

    public function getAvatarAttribute(): string
    {
        if (! empty($this->attributes['img'])) {
            return asset('storage/'.$this->attributes['img']);
        }

        return asset('images/avatar.png');
    }
}

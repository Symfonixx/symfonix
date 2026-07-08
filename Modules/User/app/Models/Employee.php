<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\CRM\Models\CrmSalesTarget;
use Modules\Finance\Models\Salary;
use Modules\Project\Models\Project;
use Modules\Project\Models\ProjectEmployee;

class Employee extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'img',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
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

    public function crmSalesTarget(): HasOne
    {
        return $this->hasOne(CrmSalesTarget::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
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

<?php

namespace Modules\Project\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\Employee;

class ProjectEmployee extends Model
{
    protected $fillable = [
        'project_id',
        'employee_id',
        'role',
        'started_at',
        'ended_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'ended_at' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('ended_at');
    }

    public function isActive(): bool
    {
        return $this->ended_at === null;
    }

    /**
     * Inclusive calendar days from start through end (or today if still active).
     */
    public function daysWorked(?CarbonInterface $asOf = null): int
    {
        $start = $this->started_at?->startOfDay();

        if ($start === null) {
            return 0;
        }

        $end = ($this->ended_at ?? $asOf ?? now())->copy()->startOfDay();

        if ($end->lt($start)) {
            return 0;
        }

        return (int) $start->diffInDays($end) + 1;
    }

    public function finish(?CarbonInterface $endedAt = null): void
    {
        $this->update([
            'ended_at' => ($endedAt ?? now())->toDateString(),
        ]);
    }
}

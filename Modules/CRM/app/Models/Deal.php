<?php

namespace Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CRM\Concerns\HasCrmTimeline;
use Modules\CRM\Enums\DealStatus;
use Modules\CRM\Filters\Deal\DealFilter;
use Modules\CRM\Support\CrmAccess;
use Modules\Project\Models\Project;
use Modules\Services\Models\Service;
use Modules\User\Models\Employee;

class Deal extends Model
{
    use HasCrmTimeline, SoftDeletes;

    public const STATUS_OPEN = DealStatus::OPEN->value;

    public const STATUS_WON = DealStatus::WON->value;

    public const STATUS_LOST = DealStatus::LOST->value;

    public const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_WON,
        self::STATUS_LOST,
    ];

    protected $fillable = [
        'title',
        'company_id',
        'lead_id',
        'pipeline_stage_id',
        'assigned_to',
        'value',
        'currency',
        'probability',
        'expected_close_date',
        'source',
        'description',
        'lost_reason',
        'status',
        'won_at',
        'lost_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'expected_close_date' => 'date',
            'won_at' => 'datetime',
            'lost_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        return (new DealFilter)->apply($query, $filters);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeVisibleTo(Builder $query, ?User $user = null): Builder
    {
        return CrmAccess::scopeDealsForUser($query, $user);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function pipelineStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function stageHistories(): HasMany
    {
        return $this->hasMany(DealStageHistory::class)->latest();
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class)->latest();
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'deal_service')
            ->withPivot(['quantity', 'unit_price'])
            ->withTimestamps();
    }
}

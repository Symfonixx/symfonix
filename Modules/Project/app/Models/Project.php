<?php

namespace Modules\Project\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Quote;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Services\CurrencyService;
use Modules\Services\Models\Service;
use Modules\Tax\Models\TaxRate;
use Modules\Testimonial\Models\Testimonial;
use Modules\User\Models\Employee;

class Project extends Model
{
    public const PAYMENT_UNPAID = 'unpaid';

    public const PAYMENT_PARTIALLY_PAID = 'partially_paid';

    public const PAYMENT_FULLY_PAID = 'fully_paid';

    protected $fillable = [
        'title',
        'description',
        'company_id',
        'project_status_id',
        'deal_id',
        'budget',
        'currency',
        'tax_rate_id',
        'budget_exchange_rate',
        'payment_status',
        'start_date',
        'due_date',
        'attachments',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'budget_exchange_rate' => 'decimal:8',
            'start_date' => 'date',
            'due_date' => 'date',
            'attachments' => 'array',
        ];
    }

    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        if (! empty($filters['company_id'])) {
            $query->where('company_id', (int) $filters['company_id']);
        }

        if (! empty($filters['project_status_id'])) {
            $query->where('project_status_id', (int) $filters['project_status_id']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ProjectStatus::class, 'project_status_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function quote(): HasOne
    {
        return $this->hasOne(Quote::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'project_service')
            ->withTimestamps();
    }

    public function useCases(): HasMany
    {
        return $this->hasMany(ProjectUseCase::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->latest('issued_at');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ProjectEmployee::class)->latest('started_at');
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'project_employees')
            ->withPivot(['id', 'role', 'started_at', 'ended_at', 'notes'])
            ->withTimestamps();
    }

    public function testimonial(): HasOne
    {
        return $this->hasOne(Testimonial::class);
    }

    public function isCompleted(): bool
    {
        if (! $this->relationLoaded('status')) {
            $this->load('status:id,name');
        }

        return strcasecmp((string) $this->status?->name, 'Completed') === 0;
    }

    public function canBeReviewedBy(User $user): bool
    {
        if (! $user->isCustomer()) {
            return false;
        }

        if (! in_array($this->company_id, $user->companyIds(), true)) {
            return false;
        }

        if (! $this->isCompleted()) {
            return false;
        }

        return ! $this->testimonial()->exists();
    }

    public function journalEntries(): MorphMany
    {
        return $this->morphMany(JournalEntry::class, 'reference');
    }

    /** @deprecated Use journalEntries() */
    public function transactions(): MorphMany
    {
        return $this->journalEntries();
    }

    /**
     * Create a project automatically when a CRM deal is won.
     * Pulls company and budget from the deal; assigns the default status.
     */
    public static function createFromDeal(Deal $deal): ?self
    {
        if ($deal->company_id === null) {
            return null;
        }

        if (static::query()->where('deal_id', $deal->id)->exists()) {
            return static::query()->where('deal_id', $deal->id)->first();
        }

        $defaultStatus = ProjectStatus::defaultStatus();

        if ($defaultStatus === null) {
            return null;
        }

        $currency = strtoupper((string) ($deal->currency
            ?? app(CurrencyService::class)->defaultCurrency()));

        $project = static::create([
            'title' => $deal->title,
            'description' => $deal->description,
            'company_id' => $deal->company_id,
            'project_status_id' => $defaultStatus->id,
            'deal_id' => $deal->id,
            'budget' => $deal->value,
            'currency' => $currency,
            'budget_exchange_rate' => app(CurrencyService::class)
                ->snapshotRateToBase($currency),
            'start_date' => now()->toDateString(),
            'due_date' => $deal->expected_close_date,
        ]);

        $project->services()->sync($deal->services()->pluck('services.id')->all());

        return $project;
    }
}

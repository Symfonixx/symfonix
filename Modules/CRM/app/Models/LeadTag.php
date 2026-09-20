<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Spatie\Translatable\HasTranslations;

class LeadTag extends Model
{
    use HasTranslations;

    public const COLORS = [
        'primary',
        'secondary',
        'success',
        'danger',
        'warning',
        'info',
        'dark',
        'light',
    ];

    protected $fillable = [
        'name',
        'color',
        'sort_order',
        'is_active',
    ];

    public array $translatable = ['name'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function leads(): BelongsToMany
    {
        return $this->belongsToMany(Lead::class, 'lead_lead_tag')
            ->withTimestamps();
    }

    /**
     * @return Collection<int, self>
     */
    public static function optionsForMarketing(string $channel): Collection
    {
        $field = $channel === 'whatsapp' ? 'phone' : 'email';

        return static::query()
            ->active()
            ->ordered()
            ->withCount([
                'leads as recipient_count' => function (Builder $query) use ($field) {
                    $query->where('blocked', false)->whereNotNull($field);
                },
            ])
            ->get();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function getDisplayNameAttribute(): string
    {
        return (string) $this->getTranslation('name', app()->getLocale())
            ?: (string) $this->getTranslation('name', 'en');
    }
}

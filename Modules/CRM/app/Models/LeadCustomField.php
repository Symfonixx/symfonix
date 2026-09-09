<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Translatable\HasTranslations;

class LeadCustomField extends Model
{
    use HasTranslations;

    public const TYPE_TEXT = 'text';

    public const TYPE_TEXTAREA = 'textarea';

    public const TYPE_NUMBER = 'number';

    public const TYPE_DATE = 'date';

    public const TYPE_SELECT = 'select';

    public const TYPE_CHECKBOX = 'checkbox';

    public const TYPES = [
        self::TYPE_TEXT,
        self::TYPE_TEXTAREA,
        self::TYPE_NUMBER,
        self::TYPE_DATE,
        self::TYPE_SELECT,
        self::TYPE_CHECKBOX,
    ];

    protected $fillable = [
        'key',
        'label',
        'type',
        'options',
        'is_required',
        'sort_order',
        'is_active',
    ];

    public array $translatable = ['label'];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function getDisplayLabelAttribute(): string
    {
        return (string) $this->getTranslation('label', app()->getLocale())
            ?: (string) $this->getTranslation('label', 'en');
    }

    public static function makeUniqueKey(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source, '_');
        $base = $base !== '' ? Str::limit($base, 60, '') : 'field';
        $base = Str::snake($base);

        $key = $base;
        $suffix = 2;

        while (static::query()
            ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
            ->where('key', $key)
            ->exists()) {
            $key = $base.'_'.$suffix;
            $suffix++;
        }

        return $key;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public static function leadValidationRules(): array
    {
        $rules = [
            'custom_fields' => ['nullable', 'array'],
        ];

        foreach (static::query()->active()->ordered()->get() as $field) {
            if ($field->type === self::TYPE_CHECKBOX) {
                $rules['custom_fields.'.$field->key] = $field->is_required
                    ? ['accepted']
                    : ['nullable', 'boolean'];

                continue;
            }

            $fieldRules = $field->is_required ? ['required'] : ['nullable'];

            $fieldRules = array_merge($fieldRules, match ($field->type) {
                self::TYPE_TEXTAREA => ['string', 'max:5000'],
                self::TYPE_NUMBER => ['numeric'],
                self::TYPE_DATE => ['date'],
                self::TYPE_SELECT => ['string', Rule::in($field->options ?? [])],
                default => ['string', 'max:255'],
            });

            $rules['custom_fields.'.$field->key] = $fieldRules;
        }

        return $rules;
    }

    public function formatValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if ($this->type === self::TYPE_CHECKBOX) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? __('Yes') : __('No');
        }

        return (string) $value;
    }
}

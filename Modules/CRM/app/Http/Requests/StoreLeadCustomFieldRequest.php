<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CRM\Models\LeadCustomField;

class StoreLeadCustomFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('crm.custom_fields.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'array'],
            'label.en' => ['required', 'string', 'max:255'],
            'label.ar' => ['nullable', 'string', 'max:255'],
            'label.de' => ['nullable', 'string', 'max:255'],
            'label.tr' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(LeadCustomField::TYPES)],
            'options_text' => [
                Rule::requiredIf(fn () => $this->input('type') === LeadCustomField::TYPE_SELECT),
                'nullable',
                'string',
            ],
            'is_required' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function validated($key = null, $default = null): mixed
    {
        $data = parent::validated($key, $default);

        if ($key !== null) {
            return $data;
        }

        $options = null;
        if (($data['type'] ?? null) === LeadCustomField::TYPE_SELECT) {
            $options = collect(preg_split('/\r\n|\r|\n/', (string) ($data['options_text'] ?? '')))
                ->map(fn ($line) => trim($line))
                ->filter()
                ->values()
                ->all();
        }

        unset($data['options_text']);

        $data['options'] = $options;
        $data['is_required'] = $this->boolean('is_required');
        $data['is_active'] = $this->boolean('is_active', true);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}

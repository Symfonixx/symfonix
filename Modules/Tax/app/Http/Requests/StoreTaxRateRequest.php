<?php

namespace Modules\Tax\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Tax\Models\TaxRate;

class StoreTaxRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('tax.rates.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'type' => ['required', Rule::in([TaxRate::TYPE_INCLUSIVE, TaxRate::TYPE_EXCLUSIVE])],
            'region_code' => ['nullable', 'string', 'max:10'],
            'status' => ['required', Rule::in([TaxRate::STATUS_ACTIVE, TaxRate::STATUS_INACTIVE])],
            'is_default' => ['nullable', 'boolean'],
        ];
    }
}

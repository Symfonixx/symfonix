<?php

namespace Modules\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('project.projects.edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'expense_category_id' => ['required', 'integer', 'exists:expense_categories,id'],
            'tax_rate_id' => ['nullable', 'integer', 'exists:tax_rates,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'transaction_date' => ['required', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('currency')) {
            $this->merge([
                'currency' => strtoupper((string) $this->input('currency')),
            ]);
        }
    }
}

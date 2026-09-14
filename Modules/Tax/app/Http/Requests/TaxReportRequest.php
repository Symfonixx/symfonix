<?php

namespace Modules\Tax\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaxReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('tax.filing.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'tax_rate_id' => ['nullable', 'exists:tax_rates,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
        ];
    }
}

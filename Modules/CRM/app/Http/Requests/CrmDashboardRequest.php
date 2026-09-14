<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CrmDashboardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'period' => ['nullable', Rule::in([
                'today', 'last_7_days', 'this_month', 'last_month', 'this_quarter', 'this_year', 'custom',
            ])],
            'assigned_to' => ['nullable', 'integer', 'exists:employees,id'],
            'date_from' => ['nullable', 'date', 'required_if:period,custom'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from', 'required_if:period,custom'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user()?->can('overview.crm_analytics.view') ?? false;
    }
}

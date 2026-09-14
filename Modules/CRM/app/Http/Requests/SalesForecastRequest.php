<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesForecastRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'assigned_to' => ['nullable', 'integer', 'exists:employees,id'],
            'pipeline_stage_id' => ['nullable', 'integer', 'exists:pipeline_stages,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'horizon' => ['nullable', 'integer', 'min:1', 'max:24'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user()?->can('sales.forecasts.view') ?? false;
    }
}

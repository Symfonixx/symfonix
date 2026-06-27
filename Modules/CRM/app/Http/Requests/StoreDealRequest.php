<?php

namespace Modules\CRM\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CRM\Models\Deal;

class StoreDealRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'company_id' => ['nullable', Rule::exists('companies', 'id')],
            'pipeline_stage_id' => ['required', Rule::exists('pipeline_stages', 'id')->where(fn ($q) => $q->where('is_active', true))],
            'assigned_to' => ['nullable', 'integer', 'exists:employees,id'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'probability' => ['nullable', 'integer', 'min:0', 'max:100'],
            'expected_close_date' => ['nullable', 'date'],
            'source' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'lost_reason' => ['nullable', 'string'],
            'status' => ['required', Rule::in([Deal::STATUS_OPEN, Deal::STATUS_WON, Deal::STATUS_LOST])],
            'services' => ['nullable', 'array'],
            'services.*.service_id' => ['nullable', 'integer', 'exists:services,id'],
            'services.*.quantity' => ['nullable', 'integer', 'min:1'],
            'services.*.unit_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CRM\Models\Lead;

class LeadIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(Lead::STATUSES)],
            'source' => ['nullable', Rule::in(Lead::SOURCES)],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:employees,id'],
        ];
    }
}

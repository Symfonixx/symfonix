<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CRM\Models\Lead;

class UpdateLeadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+\-\s()]+$/'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:employees,id'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'website' => ['nullable', 'url', 'max:255'],
            'industry' => ['nullable', 'string', 'max:150'],
            'source' => ['required', Rule::in(Lead::SOURCES)],
            'status' => ['nullable', Rule::in(Lead::STATUSES)],
            'project_budget' => ['nullable', 'string', 'max:255'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'service_interest' => ['nullable', 'string', 'max:255'],
            'problem_statement' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240'],
            'blocked' => ['sometimes', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'phone.regex' => __('crm::lead.validation.phone'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'company_id' => $this->filled('company_id') ? $this->input('company_id') : null,
            'assigned_to' => $this->filled('assigned_to') ? $this->input('assigned_to') : null,
            'blocked' => $this->boolean('blocked'),
        ]);
    }
}

<?php

namespace Modules\CRM\Http\Requests;

use App\Models\User;
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
            'phone' => ['nullable', 'string', 'max:50'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:employees,id'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'source' => ['required', Rule::in(Lead::SOURCES)],
            'project_budget' => ['nullable', 'string', 'max:255'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'service_interest' => ['nullable', 'string', 'max:255'],
            'problem_statement' => ['nullable', 'string'],
            'blocked' => ['sometimes', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'company_id' => $this->filled('company_id') ? $this->input('company_id') : null,
            'blocked' => $this->boolean('blocked'),
        ]);
    }
}

<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:255'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'blocked' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'company_id' => $this->filled('company_id') ? $this->input('company_id') : null,
            'blocked' => $this->boolean('blocked'),
        ]);
    }
}

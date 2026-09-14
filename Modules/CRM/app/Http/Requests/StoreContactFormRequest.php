<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\User\Support\PermissionCatalog;

class StoreContactFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionCatalog::userMay($this->user(), $this->route()?->getName(), $this);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:255'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['integer', 'exists:services,id'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'company_id' => $this->filled('company_id') ? $this->input('company_id') : null,
            // Legacy single column mirrors the first selected service.
            'service_id' => $this->input('service_ids.0') ?: ($this->input('service_id') ?: null),
        ]);
    }
}

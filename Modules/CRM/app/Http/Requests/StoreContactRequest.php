<?php

namespace Modules\CRM\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CRM\Models\Contact;
use Modules\User\Support\PermissionCatalog;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionCatalog::userMay($this->user(), $this->route()?->getName(), $this);
    }

    public function rules(): array
    {
        return [
            'company_id' => ['nullable', Rule::exists('companies', 'id')],
            'user_id' => ['nullable', Rule::exists('users', 'id')->where(fn ($query) => $query->where('type', User::TYPE_CUSTOMER))],
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('contacts', 'email')->whereNull('deleted_at')],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+\-\s()]+$/', Rule::unique('contacts', 'phone')->whereNull('deleted_at')],
            'phone2' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+\-\s()]+$/'],
            'source' => ['nullable', Rule::in(Contact::SOURCES)],
            'job_title' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'is_primary' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => __('crm::contact.validation.email_unique'),
            'phone.unique' => __('crm::contact.validation.phone_unique'),
            'phone.regex' => __('crm::contact.validation.phone'),
            'phone2.regex' => __('crm::contact.validation.phone'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'company_id' => $this->filled('company_id') ? $this->input('company_id') : null,
            'user_id' => $this->filled('user_id') ? $this->input('user_id') : null,
        ]);
    }
}

<?php

namespace Modules\CRM\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CRM\Models\Company;

class UpdateCompanyRequest extends FormRequest
{
    public function rules(): array
    {
        $companyId = $this->route('company')?->id ?? $this->route('company');

        return [
            'user_id' => ['required', Rule::exists('users', 'id')->where(fn ($query) => $query->where('type', User::TYPE_CUSTOMER))],
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'activity_type' => ['nullable', Rule::in(Company::ACTIVITY_TYPES)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('companies', 'email')->ignore($companyId)],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in([Company::STATUS_ACTIVE, Company::STATUS_DISABLED])],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'email.unique' => __('crm::company.validation.email_unique'),
        ];
    }
}

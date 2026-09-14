<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends StoreCompanyRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $companyId = $this->route('company')?->id ?? $this->route('company');
        $rules['email'] = ['nullable', 'email', 'max:255', Rule::unique('companies', 'email')->ignore($companyId)];

        return $rules;
    }
}

<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateContactRequest extends StoreContactRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $contactId = $this->route('contact')?->id ?? $this->route('contact');

        $rules['email'] = ['nullable', 'email', 'max:255', Rule::unique('contacts', 'email')->ignore($contactId)->whereNull('deleted_at')];
        $rules['phone'] = ['nullable', 'string', 'max:50', 'regex:/^[0-9+\-\s()]+$/', Rule::unique('contacts', 'phone')->ignore($contactId)->whereNull('deleted_at')];

        return $rules;
    }
}

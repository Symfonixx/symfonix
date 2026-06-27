<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SendMarketingEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('CRM Management') ?? false;
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:50000'],
            'all_subscribers' => ['sometimes', 'boolean'],
            'subscriber_ids' => ['sometimes', 'array'],
            'subscriber_ids.*' => ['integer', 'exists:subscribers,id'],
            'all_contacts' => ['sometimes', 'boolean'],
            'contact_ids' => ['sometimes', 'array'],
            'contact_ids.*' => ['integer', 'exists:contacts,id'],
            'all_contact_forms' => ['sometimes', 'boolean'],
            'contact_form_ids' => ['sometimes', 'array'],
            'contact_form_ids.*' => ['integer', 'exists:contact_forms,id'],
            'custom_emails' => ['nullable', 'string', 'max:10000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $hasRecipients = $this->boolean('all_subscribers')
                || filled($this->input('subscriber_ids'))
                || $this->boolean('all_contacts')
                || filled($this->input('contact_ids'))
                || $this->boolean('all_contact_forms')
                || filled($this->input('contact_form_ids'))
                || filled($this->input('custom_emails'));

            if (! $hasRecipients) {
                $validator->errors()->add('recipients', __('crm::marketing.validation.select_recipients'));
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'all_subscribers' => $this->boolean('all_subscribers'),
            'all_contacts' => $this->boolean('all_contacts'),
            'all_contact_forms' => $this->boolean('all_contact_forms'),
        ]);
    }
}

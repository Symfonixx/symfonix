<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SendMarketingEmailRequest extends FormRequest
{
    use ValidatesMarketingGroup;

    public function authorize(): bool
    {
        return $this->user()?->can('marketing.email.send') ?? false;
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:2000'],
            'body' => ['required', 'string', 'max:50000'],
            ...$this->marketingGroupRules(),
            'all_leads' => ['sometimes', 'boolean'],
            'lead_ids' => ['sometimes', 'array'],
            'lead_ids.*' => ['integer', 'exists:leads,id'],
            'lead_tag_ids' => ['sometimes', 'array'],
            'lead_tag_ids.*' => ['integer', 'exists:lead_tags,id'],
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

            $hasRecipients = $this->boolean('all_leads')
                || filled($this->input('lead_ids'))
                || filled($this->input('lead_tag_ids'))
                || $this->boolean('all_subscribers')
                || filled($this->input('subscriber_ids'))
                || $this->boolean('all_contacts')
                || filled($this->input('contact_ids'))
                || $this->boolean('all_contact_forms')
                || filled($this->input('contact_form_ids'))
                || filled($this->input('custom_emails'));

            if (! $hasRecipients) {
                $validator->errors()->add('recipients', __('crm::marketing.validation.select_recipients'));
            }

            $this->validateMarketingGroup($validator);

            $plainSubject = trim(strip_tags((string) $this->input('subject', '')));

            if ($plainSubject === '') {
                $validator->errors()->add('subject', __('crm::marketing.validation.subject_required'));
            } elseif (mb_strlen($plainSubject) > 255) {
                $validator->errors()->add('subject', __('crm::marketing.validation.subject_too_long'));
            }

            $plainBody = trim(strip_tags((string) $this->input('body', '')));

            if ($plainBody === '') {
                $validator->errors()->add('body', __('crm::marketing.validation.body_required'));
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->prepareMarketingGroup();
        $this->merge([
            'all_leads' => $this->boolean('all_leads'),
            'all_subscribers' => $this->boolean('all_subscribers'),
            'all_contacts' => $this->boolean('all_contacts'),
            'all_contact_forms' => $this->boolean('all_contact_forms'),
        ]);
    }
}

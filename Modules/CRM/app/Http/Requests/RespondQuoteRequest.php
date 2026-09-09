<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RespondQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'responder_email' => $this->filled('responder_email') ? $this->input('responder_email') : null,
            'response_note' => $this->filled('response_note') ? $this->input('response_note') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'responder_name' => ['required', 'string', 'max:120'],
            'responder_email' => ['nullable', 'email', 'max:190'],
            'response_note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'responder_name' => __('crm::quote.public.your_name'),
            'responder_email' => __('crm::quote.public.your_email'),
            'response_note' => __('crm::quote.public.optional_note'),
        ];
    }

    public function messages(): array
    {
        return [
            'responder_name.required' => __('crm::quote.validation.responder_name_required'),
            'responder_email.email' => __('crm::quote.validation.responder_email_invalid'),
        ];
    }
}

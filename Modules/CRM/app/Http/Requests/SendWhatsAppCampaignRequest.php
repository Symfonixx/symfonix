<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\CRM\Models\WhatsAppTemplate;
use Modules\CRM\Services\Marketing\WhatsAppTemplateService;

class SendWhatsAppCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('marketing.whatsapp.send') ?? false;
    }

    public function rules(): array
    {
        return [
            'whatsapp_template_id' => ['required', 'integer', 'exists:whatsapp_templates,id'],
            'template_parameters' => ['sometimes', 'array'],
            'template_parameters.*' => ['nullable', 'string', 'max:1000'],
            'all_leads' => ['sometimes', 'boolean'],
            'lead_ids' => ['sometimes', 'array'],
            'lead_ids.*' => ['integer', 'exists:leads,id'],
            'all_contacts' => ['sometimes', 'boolean'],
            'contact_ids' => ['sometimes', 'array'],
            'contact_ids.*' => ['integer', 'exists:contacts,id'],
            'all_deals' => ['sometimes', 'boolean'],
            'deal_ids' => ['sometimes', 'array'],
            'deal_ids.*' => ['integer', 'exists:deals,id'],
            'all_contact_forms' => ['sometimes', 'boolean'],
            'contact_form_ids' => ['sometimes', 'array'],
            'contact_form_ids.*' => ['integer', 'exists:contact_forms,id'],
            'custom_phones' => ['nullable', 'string', 'max:10000'],
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
                || $this->boolean('all_contacts')
                || filled($this->input('contact_ids'))
                || $this->boolean('all_deals')
                || filled($this->input('deal_ids'))
                || $this->boolean('all_contact_forms')
                || filled($this->input('contact_form_ids'))
                || filled($this->input('custom_phones'));

            if (! $hasRecipients) {
                $validator->errors()->add('recipients', __('crm::whatsapp.validation.select_recipients'));
            }

            $template = WhatsAppTemplate::query()->find($this->input('whatsapp_template_id'));

            if ($template && ! $template->isSendable()) {
                $validator->errors()->add('whatsapp_template_id', __('crm::whatsapp.validation.template_not_sendable'));
            }

            if ($template) {
                $variables = app(WhatsAppTemplateService::class)->parseBodyVariables($template->body);
                $parameters = $this->input('template_parameters', []);

                foreach ($variables as $index) {
                    $value = trim((string) ($parameters[$index] ?? ''));
                    if ($value === '') {
                        $validator->errors()->add(
                            "template_parameters.{$index}",
                            __('crm::whatsapp.validation.parameter_required', ['num' => $index]),
                        );
                    }
                }
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'all_leads' => $this->boolean('all_leads'),
            'all_contacts' => $this->boolean('all_contacts'),
            'all_deals' => $this->boolean('all_deals'),
            'all_contact_forms' => $this->boolean('all_contact_forms'),
        ]);
    }
}

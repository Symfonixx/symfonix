<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\CRM\Models\WhatsAppTemplate;
use Modules\CRM\Services\Marketing\WhatsAppTemplateService;

class SendWhatsAppCampaignRequest extends FormRequest
{
    use ValidatesMarketingGroup;

    public function authorize(): bool
    {
        return $this->user()?->can('marketing.whatsapp.send') ?? false;
    }

    public function rules(): array
    {
        return [
            ...$this->marketingGroupRules(),
            'whatsapp_template_id' => ['required', 'integer', 'exists:whatsapp_templates,id'],
            'template_parameters' => ['sometimes', 'array'],
            'template_parameters.header' => ['sometimes', 'array'],
            'template_parameters.header.*' => ['nullable', 'string', 'max:1000'],
            'template_parameters.body' => ['sometimes', 'array'],
            'template_parameters.body.*' => ['nullable', 'string', 'max:1000'],
            'template_parameters.buttons' => ['sometimes', 'array'],
            'template_parameters.buttons.*' => ['sometimes', 'array'],
            'template_parameters.buttons.*.*' => ['nullable', 'string', 'max:1000'],
            'all_leads' => ['sometimes', 'boolean'],
            'lead_ids' => ['sometimes', 'array'],
            'lead_ids.*' => ['integer', 'exists:leads,id'],
            'lead_tag_ids' => ['sometimes', 'array'],
            'lead_tag_ids.*' => ['integer', 'exists:lead_tags,id'],
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
                || filled($this->input('lead_tag_ids'))
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

            $this->validateMarketingGroup($validator);

            $template = WhatsAppTemplate::query()->find($this->input('whatsapp_template_id'));

            if ($template && ! $template->isSendable()) {
                $validator->errors()->add('whatsapp_template_id', __('crm::whatsapp.validation.template_not_sendable'));
            }

            if ($template) {
                $variables = app(WhatsAppTemplateService::class)->collectVariables($template);
                $parameters = $this->input('template_parameters', []);

                foreach ($variables as $variable) {
                    $value = match ($variable['component']) {
                        'header' => trim((string) data_get($parameters, 'header.'.$variable['index'], '')),
                        'buttons' => trim((string) data_get(
                            $parameters,
                            'buttons.'.$variable['button_index'].'.'.$variable['index'],
                            '',
                        )),
                        default => trim((string) data_get(
                            $parameters,
                            'body.'.$variable['index'],
                            data_get($parameters, (string) $variable['index'], ''),
                        )),
                    };

                    if ($value === '') {
                        $validator->errors()->add(
                            $this->parameterErrorKey($variable),
                            __('crm::whatsapp.validation.parameter_required_named', ['label' => $variable['label']]),
                        );
                    }
                }
            }
        });
    }

    /**
     * @param  array{component: string, index: int, button_index?: int}  $variable
     */
    private function parameterErrorKey(array $variable): string
    {
        return match ($variable['component']) {
            'header' => "template_parameters.header.{$variable['index']}",
            'buttons' => "template_parameters.buttons.{$variable['button_index']}.{$variable['index']}",
            default => "template_parameters.body.{$variable['index']}",
        };
    }

    protected function prepareForValidation(): void
    {
        $this->prepareMarketingGroup();
        $this->merge([
            'all_leads' => $this->boolean('all_leads'),
            'all_contacts' => $this->boolean('all_contacts'),
            'all_deals' => $this->boolean('all_deals'),
            'all_contact_forms' => $this->boolean('all_contact_forms'),
        ]);
    }
}

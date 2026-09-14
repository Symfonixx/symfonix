<?php

namespace Modules\CRM\Http\Requests;

use Modules\CRM\Models\WhatsAppTemplate;

class UpdateWhatsAppTemplateRequest extends StoreWhatsAppTemplateRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('marketing.whatsapp_templates.edit') ?? false;
    }

    public function rules(): array
    {
        /** @var WhatsAppTemplate $template */
        $template = $this->route('template');

        return $this->templateRules($template->id);
    }

    protected function prepareForValidation(): void
    {
        $buttons = collect($this->input('buttons', []))
            ->filter(fn (mixed $button) => is_array($button) && filled($button['text'] ?? null))
            ->values()
            ->all();

        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'buttons' => empty($buttons) ? null : $buttons,
        ]);
    }
}

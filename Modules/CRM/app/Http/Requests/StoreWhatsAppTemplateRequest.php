<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CRM\Enums\WhatsAppButtonType;
use Modules\CRM\Enums\WhatsAppTemplateCategory;
use Modules\CRM\Models\WhatsAppTemplate;

class StoreWhatsAppTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('marketing.whatsapp_templates.create') ?? false;
    }

    public function rules(): array
    {
        return $this->templateRules();
    }

    /**
     * @return array<string, mixed>
     */
    protected function templateRules(?int $ignoreId = null): array
    {
        $uniqueName = Rule::unique('whatsapp_templates')
            ->where(fn ($q) => $q->where('language', $this->input('language', 'en')));

        if ($ignoreId !== null) {
            $uniqueName = $uniqueName->ignore($ignoreId);
        }

        return [
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/', $uniqueName],
            'language' => ['required', 'string', 'max:10'],
            'category' => ['required', 'string', Rule::in(WhatsAppTemplateCategory::values())],
            'status' => ['required', 'string', Rule::in([
                WhatsAppTemplate::STATUS_APPROVED,
                WhatsAppTemplate::STATUS_PENDING,
                WhatsAppTemplate::STATUS_REJECTED,
                WhatsAppTemplate::STATUS_DRAFT,
            ])],
            'header_type' => ['required', 'string', Rule::in([
                WhatsAppTemplate::HEADER_NONE,
                WhatsAppTemplate::HEADER_TEXT,
                WhatsAppTemplate::HEADER_IMAGE,
                WhatsAppTemplate::HEADER_VIDEO,
                WhatsAppTemplate::HEADER_DOCUMENT,
            ])],
            'header_content' => ['nullable', 'string', 'max:2000'],
            'body' => ['required', 'string', 'max:1024'],
            'footer' => ['nullable', 'string', 'max:60'],
            'buttons' => ['nullable', 'array', 'max:3'],
            'buttons.*.type' => ['required_with:buttons', 'string', Rule::in(WhatsAppButtonType::values())],
            'buttons.*.text' => ['required_with:buttons', 'string', 'max:25'],
            'buttons.*.url' => ['nullable', 'string', 'max:2000'],
            'buttons.*.phone_number' => ['nullable', 'string', 'max:20'],
            'meta_template_id' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $buttons = collect($this->input('buttons', []))
            ->filter(fn (mixed $button) => is_array($button) && filled($button['text'] ?? null))
            ->values()
            ->all();

        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'buttons' => empty($buttons) ? null : $buttons,
        ]);
    }
}

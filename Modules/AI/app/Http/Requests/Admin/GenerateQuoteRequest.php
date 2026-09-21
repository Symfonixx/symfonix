<?php

namespace Modules\AI\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('sales.quotes.create') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', Rule::exists('companies', 'id')],
            'deal_id' => [
                'required',
                'integer',
                Rule::exists('deals', 'id')->where(
                    fn ($query) => $query->where('company_id', (int) $this->input('company_id'))
                ),
            ],
            'prompt' => ['nullable', 'string', 'max:2000'],
            'locale' => ['nullable', 'string', 'max:12'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'company_id.required' => __('crm::quote.ai.company_required'),
            'company_id.exists' => __('crm::quote.validation.company_exists'),
            'deal_id.required' => __('crm::quote.ai.deal_required'),
            'deal_id.exists' => __('crm::quote.validation.deal_exists'),
        ];
    }

    public function companyId(): int
    {
        return (int) $this->validated('company_id');
    }

    public function dealId(): int
    {
        return (int) $this->validated('deal_id');
    }

    public function prompt(): ?string
    {
        $prompt = $this->validated('prompt');

        return is_string($prompt) && $prompt !== '' ? $prompt : null;
    }

    public function locale(): string
    {
        return (string) ($this->validated('locale') ?: app()->getLocale());
    }
}

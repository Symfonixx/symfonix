<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CRM\Models\QuoteLine;

class StoreQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('sales.quotes.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $lines = collect($this->input('lines', []))
            ->map(function (array $line) {
                $type = $line['item_type'] ?? QuoteLine::TYPE_SERVICE;

                if ($type === QuoteLine::TYPE_SERVICE) {
                    $line['product_id'] = null;
                    $line['service_id'] = $line['service_id'] ?: null;
                } else {
                    $line['service_id'] = null;
                    $line['product_id'] = $line['product_id'] ?: null;
                }

                $line['discount_percent'] = $line['discount_percent'] ?? 0;
                $line['tax_percent'] = $line['tax_percent'] ?? 0;
                $line['description'] = isset($line['description']) && $line['description'] !== ''
                    ? $line['description']
                    : null;

                return $line;
            })
            ->values()
            ->all();

        $this->merge([
            'company_id' => $this->filled('company_id') ? (int) $this->input('company_id') : null,
            'deal_id' => $this->filled('deal_id') ? (int) $this->input('deal_id') : null,
            'currency' => strtoupper(trim((string) $this->input('currency', ''))),
            'expires_at' => $this->filled('expires_at') ? $this->input('expires_at') : null,
            'terms' => $this->filled('terms') ? $this->input('terms') : null,
            'notes' => $this->filled('notes') ? $this->input('notes') : null,
            'lines' => $lines,
        ]);
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', Rule::exists('companies', 'id')],
            'deal_id' => [
                'required',
                'integer',
                Rule::exists('deals', 'id')->where(
                    fn ($q) => $q->where('company_id', (int) $this->input('company_id'))
                ),
            ],
            'currency' => ['required', 'string', 'size:3'],
            'issued_at' => ['required', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'terms' => ['nullable', 'string', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.item_type' => ['required', Rule::in([QuoteLine::TYPE_SERVICE, QuoteLine::TYPE_PRODUCT])],
            'lines.*.service_id' => [
                'nullable',
                'required_if:lines.*.item_type,'.QuoteLine::TYPE_SERVICE,
                'integer',
                Rule::exists('services', 'id'),
            ],
            'lines.*.product_id' => [
                'nullable',
                'required_if:lines.*.item_type,'.QuoteLine::TYPE_PRODUCT,
                'integer',
                Rule::exists('products', 'id'),
            ],
            'lines.*.description' => ['nullable', 'string', 'max:255'],
            'lines.*.quantity' => ['required', 'integer', 'min:1'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
            'lines.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'lines.*.tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'company_id' => __('crm::quote.fields.company'),
            'deal_id' => __('crm::quote.fields.deal'),
            'currency' => __('crm::quote.fields.currency'),
            'issued_at' => __('crm::quote.fields.issued_at'),
            'expires_at' => __('crm::quote.fields.expires_at'),
            'terms' => __('crm::quote.fields.terms'),
            'notes' => __('crm::quote.fields.notes'),
            'lines' => __('crm::quote.fields.line_items'),
            'lines.*.item_type' => __('crm::quote.fields.item_type'),
            'lines.*.service_id' => __('crm::quote.fields.service'),
            'lines.*.product_id' => __('crm::quote.fields.product'),
            'lines.*.description' => __('crm::quote.fields.description'),
            'lines.*.quantity' => __('crm::quote.fields.quantity'),
            'lines.*.unit_price' => __('crm::quote.fields.unit_price'),
            'lines.*.discount_percent' => __('crm::quote.fields.discount_percent'),
            'lines.*.tax_percent' => __('crm::quote.fields.tax_percent'),
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required' => __('crm::quote.validation.company_required'),
            'company_id.exists' => __('crm::quote.validation.company_exists'),
            'deal_id.required' => __('crm::quote.validation.deal_required'),
            'deal_id.exists' => __('crm::quote.validation.deal_exists'),
            'currency.required' => __('crm::quote.validation.currency_required'),
            'currency.size' => __('crm::quote.validation.currency_size'),
            'issued_at.required' => __('crm::quote.validation.issued_at_required'),
            'issued_at.date' => __('crm::quote.validation.issued_at_date'),
            'expires_at.date' => __('crm::quote.validation.expires_at_date'),
            'expires_at.after_or_equal' => __('crm::quote.validation.expires_at_after'),
            'lines.required' => __('crm::quote.validation.lines_required'),
            'lines.min' => __('crm::quote.validation.lines_required'),
            'lines.*.item_type.required' => __('crm::quote.validation.item_type_required'),
            'lines.*.item_type.in' => __('crm::quote.validation.item_type_in'),
            'lines.*.service_id.required_if' => __('crm::quote.validation.service_required'),
            'lines.*.service_id.exists' => __('crm::quote.validation.service_exists'),
            'lines.*.product_id.required_if' => __('crm::quote.validation.product_required'),
            'lines.*.product_id.exists' => __('crm::quote.validation.product_exists'),
            'lines.*.quantity.required' => __('crm::quote.validation.quantity_required'),
            'lines.*.quantity.min' => __('crm::quote.validation.quantity_min'),
            'lines.*.unit_price.required' => __('crm::quote.validation.unit_price_required'),
            'lines.*.unit_price.min' => __('crm::quote.validation.unit_price_min'),
            'lines.*.discount_percent.max' => __('crm::quote.validation.discount_max'),
            'lines.*.tax_percent.max' => __('crm::quote.validation.tax_max'),
        ];
    }
}

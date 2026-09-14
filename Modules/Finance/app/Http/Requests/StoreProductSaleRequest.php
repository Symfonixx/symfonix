<?php

namespace Modules\Finance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('finance.product_sales.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'deal_id' => ['nullable', 'integer', 'exists:deals,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'tax_rate_id' => ['nullable', 'integer', 'exists:tax_rates,id'],
            'notes' => ['nullable', 'string'],
            'sold_at' => ['nullable', 'date'],
        ];
    }
}

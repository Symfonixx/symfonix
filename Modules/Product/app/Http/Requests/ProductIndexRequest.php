<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Product\Models\Product;

class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('product.catalog.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'product_category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'status' => ['nullable', Rule::in([Product::STATUS_ACTIVE, Product::STATUS_ARCHIVED])],
            'is_published' => ['nullable', Rule::in(['0', '1', ''])],
        ];
    }
}

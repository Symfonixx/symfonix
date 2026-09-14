<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Product\Enums\ProductBillingType;
use Modules\Product\Enums\ProductStatus;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('product.catalog.edit') ?? false;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'product_category_id' => ['required', 'integer', 'exists:product_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'sku' => ['nullable', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($productId)],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'main_image' => ['nullable', 'image', 'max:5120'],
            'seo_meta_img' => ['nullable', 'image', 'max:5120'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'tax_rate_id' => ['nullable', 'integer', 'exists:tax_rates,id'],
            'billing_type' => ['required', Rule::in(ProductBillingType::values())],
            'status' => ['required', Rule::in(ProductStatus::values())],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'auto_translate' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'is_published' => $this->boolean('is_published'),
            'auto_translate' => $this->boolean('auto_translate'),
        ]);
    }
}

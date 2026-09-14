<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('product.categories.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:product_categories,name'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:product_categories,slug'],
            'description' => ['nullable', 'string'],
            'auto_translate' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'auto_translate' => $this->boolean('auto_translate'),
        ]);
    }
}

<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('product.categories.edit') ?? false;
    }

    public function rules(): array
    {
        $category = $this->route('product_category');
        $categoryId = is_object($category) ? $category->id : $category;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_categories', 'name')->ignore($categoryId),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('product_categories', 'slug')->ignore($categoryId),
            ],
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

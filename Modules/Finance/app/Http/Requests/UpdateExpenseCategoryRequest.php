<?php

namespace Modules\Finance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('finance.expense_categories.edit') ?? false;
    }

    public function rules(): array
    {
        $category = $this->route('expense_category');
        $categoryId = is_object($category) ? $category->id : $category;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('expense_categories', 'name')->ignore($categoryId),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('expense_categories', 'slug')->ignore($categoryId),
            ],
        ];
    }
}

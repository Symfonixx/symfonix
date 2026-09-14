<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('hr.employees.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:50'],
            'email' => ['required', 'email', 'max:255', Rule::unique('employees', 'email')],
            'mobile' => ['required', 'numeric', 'digits_between:10,15', Rule::unique('employees', 'mobile')],
            'position' => ['nullable', 'string', 'max:120'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('position') === '') {
            $this->merge(['position' => null]);
        }
    }
}

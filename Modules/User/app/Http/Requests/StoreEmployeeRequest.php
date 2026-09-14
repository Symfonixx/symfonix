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
        ];
    }
}

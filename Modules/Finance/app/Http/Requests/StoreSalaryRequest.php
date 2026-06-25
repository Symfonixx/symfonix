<?php

namespace Modules\Finance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Finance\Models\Salary;

class StoreSalaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('Finance Management') ?? false;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in([Salary::STATUS_PENDING, Salary::STATUS_PAID])],
        ];
    }
}

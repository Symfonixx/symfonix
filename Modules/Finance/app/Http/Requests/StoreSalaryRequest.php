<?php

namespace Modules\Finance\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Finance\Models\Salary;

class StoreSalaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('finance.salaries.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('period')) {
            $this->merge([
                'period' => Carbon::createFromFormat('Y-m', $this->input('period'))
                    ->startOfMonth()
                    ->toDateString(),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'period' => [
                'required',
                'date',
                Rule::unique('salaries')->where(fn ($query) => $query->where('employee_id', $this->input('employee_id'))),
            ],
            'status' => ['nullable', Rule::in([Salary::STATUS_PENDING, Salary::STATUS_PAID])],
        ];
    }

    public function messages(): array
    {
        return [
            'period.unique' => __('finance::salary.messages.period_exists'),
        ];
    }

    public function attributes(): array
    {
        return [
            'period' => __('finance::salary.fields.period'),
        ];
    }
}

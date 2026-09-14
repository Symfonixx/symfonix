<?php

namespace Modules\Reporting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\User\Support\PermissionCatalog;

class ReportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionCatalog::userMay($this->user(), $this->route()?->getName(), $this);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'period' => ['nullable', 'string', 'in:today,last_7_days,this_month,last_month,this_quarter,this_year,custom'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'assigned_to' => ['nullable', 'integer', 'exists:employees,id'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'category_id' => ['nullable', 'integer'],
            'currency' => ['nullable', 'string', 'max:3'],
            'format' => ['nullable', 'string', 'in:csv,pdf'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        return $this->only(['period', 'date_from', 'date_to', 'assigned_to', 'employee_id', 'category_id', 'currency']);
    }
}

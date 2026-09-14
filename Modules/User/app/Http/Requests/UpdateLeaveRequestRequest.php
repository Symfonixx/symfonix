<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Models\LeaveRequest;

class UpdateLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('hr.leaves.edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', Rule::exists('employees', 'id')],
            'type' => ['required', Rule::in(array_keys(LeaveRequest::types()))],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(array_keys(LeaveRequest::statuses()))],
            'reason' => ['nullable', 'string', 'max:1000'],
            'manager_note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

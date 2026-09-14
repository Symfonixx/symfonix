<?php

namespace Modules\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('project.projects.edit') ?? false;
    }

    public function rules(): array
    {
        $projectId = (int) $this->route('project')?->id;

        return [
            'employee_id' => [
                'required',
                'integer',
                'exists:employees,id',
                Rule::unique('project_employees', 'employee_id')
                    ->where(fn ($query) => $query
                        ->where('project_id', $projectId)
                        ->whereNull('ended_at')),
            ],
            'role' => ['nullable', 'string', 'max:100'],
            'started_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.unique' => __('project::project.messages.employee_already_assigned'),
        ];
    }
}

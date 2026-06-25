<?php

namespace Modules\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('Project Management') ?? false;
    }

    public function rules(): array
    {
        $statusId = $this->route('project_status')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('project_statuses', 'name')->ignore($statusId),
            ],
            'color_code' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ];
    }
}

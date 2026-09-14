<?php

namespace Modules\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('project.statuses.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:project_statuses,name'],
            'color_code' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ];
    }
}

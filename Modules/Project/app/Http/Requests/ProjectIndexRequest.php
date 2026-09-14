<?php

namespace Modules\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('project.projects.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'project_status_id' => ['nullable', 'integer', 'exists:project_statuses,id'],
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }
}

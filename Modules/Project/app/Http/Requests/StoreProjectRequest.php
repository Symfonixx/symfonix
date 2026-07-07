<?php

namespace Modules\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Project\Rules\ProjectAttachmentFile;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('Project Management') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'project_status_id' => ['required', 'integer', 'exists:project_statuses,id'],
            'deal_id' => ['nullable', 'integer', 'exists:deals,id', 'unique:projects,deal_id'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240', new ProjectAttachmentFile],
        ];
    }
}

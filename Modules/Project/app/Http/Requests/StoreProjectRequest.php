<?php

namespace Modules\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Project\Rules\ProjectAttachmentFile;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('project.projects.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'project_status_id' => ['required', 'integer', 'exists:project_statuses,id'],
            'deal_id' => ['nullable', 'integer', 'exists:deals,id', 'unique:projects,deal_id'],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['integer', 'exists:services,id'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'tax_rate_id' => ['nullable', 'integer', 'exists:tax_rates,id'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240', new ProjectAttachmentFile],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('currency')) {
            $this->merge([
                'currency' => strtoupper((string) $this->input('currency')),
            ]);
        }
    }
}

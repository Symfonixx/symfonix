<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CRM\Models\Lead;
use Modules\User\Support\PermissionCatalog;

class LeadIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionCatalog::userMay($this->user(), $this->route()?->getName(), $this);
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(Lead::STATUSES)],
            'source' => ['nullable', Rule::in(Lead::SOURCES)],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:employees,id'],
            'tag_id' => ['nullable', 'integer', 'exists:lead_tags,id'],
        ];
    }
}

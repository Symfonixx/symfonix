<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CRM\Models\Deal;
use Modules\User\Support\PermissionCatalog;

class DealIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in([Deal::STATUS_OPEN, Deal::STATUS_WON, Deal::STATUS_LOST])],
            'pipeline_stage_id' => ['nullable', 'integer', Rule::exists('pipeline_stages', 'id')],
            'company_id' => ['nullable', 'integer', Rule::exists('companies', 'id')],
            'assigned_to' => ['nullable', 'integer', Rule::exists('employees', 'id')],
            'tag_id' => ['nullable', 'integer', Rule::exists('lead_tags', 'id')],
            'with_trashed' => ['nullable', 'boolean'],
            'view' => ['nullable', Rule::in(['list', 'kanban'])],
        ];
    }

    public function authorize(): bool
    {
        return PermissionCatalog::userMay($this->user(), $this->route()?->getName(), $this);
    }
}

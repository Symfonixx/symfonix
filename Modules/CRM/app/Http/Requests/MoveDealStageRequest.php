<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Support\PermissionCatalog;

class MoveDealStageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pipeline_stage_id' => ['required', 'integer', Rule::exists('pipeline_stages', 'id')->where(fn ($q) => $q->where('is_active', true))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function authorize(): bool
    {
        return PermissionCatalog::userMay($this->user(), $this->route()?->getName(), $this);
    }
}

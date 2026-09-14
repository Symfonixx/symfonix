<?php

namespace Modules\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Support\PermissionCatalog;

class UpdateProjectStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'project_status_id' => ['required', 'integer', Rule::exists('project_statuses', 'id')],
        ];
    }

    public function authorize(): bool
    {
        return PermissionCatalog::userMay($this->user(), $this->route()?->getName(), $this);
    }
}

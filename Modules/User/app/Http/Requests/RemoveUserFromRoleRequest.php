<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\User\Support\PermissionCatalog;

class RemoveUserFromRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionCatalog::userMay($this->user(), $this->route()?->getName(), $this);
    }

    public function rules(): array
    {
        return [
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}

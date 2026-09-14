<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Support\PermissionCatalog;

class RoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'role_name' => 'required|string|min:2',
            'permissions' => 'required|array',
            'permissions.*' => ['string', Rule::in(PermissionCatalog::allKeys())],
        ];
    }

    public function authorize(): bool
    {
        $permission = $this->isMethod('PUT') || $this->isMethod('PATCH')
            ? 'hr.roles.edit'
            : 'hr.roles.create';

        return $this->user()?->can($permission) ?? false;
    }
}

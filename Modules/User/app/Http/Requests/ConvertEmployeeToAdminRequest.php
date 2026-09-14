<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Support\PermissionCatalog;

class ConvertEmployeeToAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('hr.admins.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:6', 'max:100'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(PermissionCatalog::allKeys())],
        ];
    }
}

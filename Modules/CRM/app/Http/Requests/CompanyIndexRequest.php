<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CRM\Models\Company;
use Modules\User\Support\PermissionCatalog;

class CompanyIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in([Company::STATUS_ACTIVE, Company::STATUS_DISABLED])],
            'country' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'activity_type' => ['nullable', Rule::in(Company::ACTIVITY_TYPES)],
            'with_trashed' => ['nullable', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return PermissionCatalog::userMay($this->user(), $this->route()?->getName(), $this);
    }
}

<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\User\Support\PermissionCatalog;

class EnrollFingerprintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionCatalog::userMay($this->user(), $this->route()?->getName(), $this);
    }

    public function rules(): array
    {
        return [
            'finger_index' => ['nullable', 'integer', 'min:0', 'max:9'],
        ];
    }
}

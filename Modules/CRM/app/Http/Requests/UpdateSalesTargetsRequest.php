<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSalesTargetsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('CRM Management') ?? false;
    }

    public function rules(): array
    {
        return [
            'targets' => ['required', 'array'],
            'targets.*.user_id' => ['required', 'integer', 'exists:users,id'],
            'targets.*.deals_target' => ['required', 'integer', 'min:1', 'max:9999'],
            'targets.*.value_target' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

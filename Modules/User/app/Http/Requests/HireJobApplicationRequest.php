<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HireJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('hr.employees.create') ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}

<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddEmployeeToTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('cms.team.create') ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}

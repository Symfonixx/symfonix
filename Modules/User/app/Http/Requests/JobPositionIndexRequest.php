<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Models\JobPosition;

class JobPositionIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('hr.job_positions.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in([JobPosition::STATUS_ACTIVE, JobPosition::STATUS_CLOSED])],
        ];
    }
}

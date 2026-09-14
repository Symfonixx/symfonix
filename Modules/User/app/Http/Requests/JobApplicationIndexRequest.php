<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Models\JobApplication;

class JobApplicationIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('hr.job_applications.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'position_id' => ['nullable', 'integer', 'exists:job_positions,id'],
            'status' => ['nullable', Rule::in(JobApplication::STATUSES)],
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }
}

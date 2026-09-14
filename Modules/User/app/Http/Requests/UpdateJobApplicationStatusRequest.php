<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Models\JobApplication;

class UpdateJobApplicationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('hr.job_applications.edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(JobApplication::STATUSES)],
            'profile_notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}

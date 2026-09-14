<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Models\JobPosition;

class StoreJobPositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('hr.job_positions.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'employment_type' => ['required', Rule::in(JobPosition::EMPLOYMENT_TYPES)],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
            'status' => ['required', Rule::in([JobPosition::STATUS_ACTIVE, JobPosition::STATUS_CLOSED])],
            'posted_at' => ['required', 'date'],
        ];
    }
}

<?php

namespace Modules\User\Http\Requests;

class UpdateJobPositionRequest extends StoreJobPositionRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('hr.job_positions.edit') ?? false;
    }
}

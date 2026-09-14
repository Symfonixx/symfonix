<?php

namespace Modules\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Project\Models\ProjectEmployee;

class FinishProjectEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('project.projects.edit') ?? false;
    }

    public function rules(): array
    {
        /** @var ProjectEmployee|null $assignment */
        $assignment = $this->route('assignment');

        return [
            'ended_at' => [
                'nullable',
                'date',
                $assignment ? 'after_or_equal:'.$assignment->started_at?->toDateString() : 'date',
            ],
        ];
    }
}

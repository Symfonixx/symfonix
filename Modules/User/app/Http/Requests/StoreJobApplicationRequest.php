<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Models\JobPosition;

class StoreJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $position = $this->route('position');

        return $position instanceof JobPosition && $position->status === JobPosition::STATUS_ACTIVE;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'expected_salary' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'motivation' => ['required', 'string', 'max:2000'],
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'cover_letter' => ['nullable', 'string', 'max:10000'],
        ];
    }
}

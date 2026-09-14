<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CRM\Models\CrmActivity;
use Modules\CRM\Support\CrmSubjectResolver;

class StoreActivityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'subject_type' => ['required', 'string', Rule::in(array_keys(CrmSubjectResolver::MAP))],
            'subject_id' => ['required', 'integer', 'min:1'],
            'type' => ['required', Rule::in(CrmActivity::TYPES)],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'scheduled_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user()?->can('crm.activities.create') ?? false;
    }
}

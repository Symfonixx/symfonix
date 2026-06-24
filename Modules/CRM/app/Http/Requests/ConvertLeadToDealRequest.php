<?php

namespace Modules\CRM\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConvertLeadToDealRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'min:2', 'max:255'],
            'pipeline_stage_id' => ['nullable', 'integer', Rule::exists('pipeline_stages', 'id')->where(fn ($q) => $q->where('is_active', true))],
            'assigned_to' => ['nullable', Rule::exists('users', 'id')->where(fn ($q) => $q->whereIn('type', [User::TYPE_EMPLOYEE, User::TYPE_ADMIN]))],
            'expected_close_date' => ['nullable', 'date'],
            'currency' => ['nullable', 'string', 'size:3'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user()?->can('CRM Management') ?? false;
    }
}

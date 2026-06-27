<?php

namespace Modules\Finance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordSalaryPayoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('salary')) ?? false;
    }

    public function rules(): array
    {
        return [
            'paid_at' => ['required', 'date'],
        ];
    }
}

<?php

namespace Modules\AI\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplyImageEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'action' => ['required', Rule::in(['replace', 'new_version'])],
        ];
    }

    public function token(): string
    {
        return (string) $this->validated('token');
    }

    public function action(): string
    {
        return (string) $this->validated('action');
    }
}

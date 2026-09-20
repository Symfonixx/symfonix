<?php

namespace Modules\AI\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SendAssistantMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('ai.assistant.view') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:4000'],
        ];
    }
}

<?php

namespace Modules\AI\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GenerateEditorContentRequest extends FormRequest
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
            'prompt' => ['required', 'string', 'max:2000'],
            'context' => ['nullable', 'string', 'max:8000'],
        ];
    }

    public function prompt(): string
    {
        return (string) $this->validated('prompt');
    }

    public function context(): ?string
    {
        $context = $this->validated('context');

        return is_string($context) ? $context : null;
    }
}

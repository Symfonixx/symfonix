<?php

namespace Modules\AI\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Modules\AI\Support\FormContentSchema;

class GenerateFormContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $type = (string) $this->input('form_type', '');

        if ($user === null) {
            return false;
        }

        if (! in_array($type, FormContentSchema::types(), true)) {
            return true;
        }

        return $user->canany(FormContentSchema::permissions($type));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'prompt' => [
                Rule::requiredIf($this->mode() !== FormContentSchema::MODE_OPTIMIZE),
                'nullable',
                'string',
                'max:2000',
            ],
            'form_type' => ['required', 'string', Rule::in(FormContentSchema::types())],
            'mode' => ['nullable', 'string', Rule::in(FormContentSchema::modes())],
            'locale' => ['nullable', 'string', 'max:12'],
            'existing' => ['nullable', 'array'],
            'existing.*' => ['nullable', 'string', 'max:40000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty() || $this->mode() !== FormContentSchema::MODE_OPTIMIZE) {
                return;
            }

            if (FormContentSchema::filledExisting($this->input('existing')) === []) {
                $validator->errors()->add('existing', __('ai::content_generation.messages.empty_content'));
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'mode' => FormContentSchema::normalizeMode($this->input('mode')),
            'prompt' => is_string($this->input('prompt')) ? $this->input('prompt') : '',
        ]);
    }

    public function formType(): string
    {
        return (string) $this->validated('form_type');
    }

    public function prompt(): string
    {
        return (string) ($this->validated('prompt') ?? '');
    }

    public function locale(): string
    {
        return (string) ($this->validated('locale') ?: app()->getLocale());
    }

    public function mode(): string
    {
        return FormContentSchema::normalizeMode($this->input('mode'));
    }

    /**
     * @return array<string, string>
     */
    public function existing(): array
    {
        return FormContentSchema::filledExisting($this->validated('existing') ?? $this->input('existing'));
    }
}

<?php

namespace Modules\AI\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GenerateLeadFollowUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && $user->can('crm.leads.view')
            && $user->can('crm.activities.create');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'prompt' => ['nullable', 'string', 'max:2000'],
            'locale' => ['nullable', 'string', 'max:12'],
        ];
    }

    public function prompt(): ?string
    {
        $prompt = $this->validated('prompt');

        return is_string($prompt) && $prompt !== '' ? $prompt : null;
    }

    public function locale(): string
    {
        return (string) ($this->validated('locale') ?: app()->getLocale());
    }
}

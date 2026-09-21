<?php

namespace Modules\AI\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\CRM\Models\MarketingGroup;

class GenerateMarketingEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('marketing.email.send') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'marketing_group_id' => ['nullable', 'integer', 'exists:marketing_groups,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'goal' => ['nullable', 'string', 'max:5000'],
            'prompt' => ['nullable', 'string', 'max:2000'],
            'locale' => ['nullable', 'string', 'max:12'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if (filled($this->input('marketing_group_id'))) {
                return;
            }

            if (trim((string) $this->input('title', '')) === '' || trim((string) $this->input('goal', '')) === '') {
                $validator->errors()->add('goal', __('crm::marketing.ai.goal_required'));
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $groupId = $this->input('marketing_group_id');

        if ($groupId === '' || $groupId === '0' || $groupId === 0) {
            $this->merge(['marketing_group_id' => null]);
        }
    }

    public function marketingGroup(): ?MarketingGroup
    {
        $id = $this->validated('marketing_group_id');

        return is_numeric($id) ? MarketingGroup::query()->find((int) $id) : null;
    }

    public function title(): ?string
    {
        $title = $this->validated('title');

        return is_string($title) && $title !== '' ? $title : null;
    }

    public function goal(): ?string
    {
        $goal = $this->validated('goal');

        return is_string($goal) && $goal !== '' ? $goal : null;
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

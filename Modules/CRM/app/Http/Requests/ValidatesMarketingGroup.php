<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Validation\Validator;

trait ValidatesMarketingGroup
{
    /**
     * @return array<string, mixed>
     */
    protected function marketingGroupRules(): array
    {
        return [
            'marketing_group_id' => ['nullable', 'integer', 'exists:marketing_groups,id'],
            'group_title' => ['nullable', 'string', 'max:255'],
            'group_goal' => ['nullable', 'string', 'max:5000'],
        ];
    }

    protected function prepareMarketingGroup(): void
    {
        $groupId = $this->input('marketing_group_id');

        if ($groupId === '' || $groupId === '0' || $groupId === 0) {
            $this->merge(['marketing_group_id' => null]);
        }
    }

    protected function validateMarketingGroup(Validator $validator): void
    {
        if (filled($this->input('marketing_group_id'))) {
            return;
        }

        if (trim((string) $this->input('group_title', '')) === '') {
            $validator->errors()->add('group_title', __('crm::marketing.validation.group_title_required'));
        }

        if (trim((string) $this->input('group_goal', '')) === '') {
            $validator->errors()->add('group_goal', __('crm::marketing.validation.group_goal_required'));
        }
    }
}

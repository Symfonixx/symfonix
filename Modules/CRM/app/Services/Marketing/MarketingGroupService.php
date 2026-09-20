<?php

namespace Modules\CRM\Services\Marketing;

use Illuminate\Support\Collection;
use Modules\CRM\Models\MarketingGroup;

class MarketingGroupService
{
    /**
     * @return Collection<int, MarketingGroup>
     */
    public function formOptions(): Collection
    {
        return MarketingGroup::query()
            ->withCount(['emailCampaigns', 'whatsappCampaigns'])
            ->latest()
            ->get();
    }

    public function create(int $userId, string $title, string $goal): MarketingGroup
    {
        return MarketingGroup::query()->create([
            'user_id' => $userId,
            'title' => trim($title),
            'goal' => trim($goal),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function resolveFromPayload(array $data, int $userId): MarketingGroup
    {
        $groupId = (int) ($data['marketing_group_id'] ?? 0);

        if ($groupId > 0) {
            return MarketingGroup::query()->findOrFail($groupId);
        }

        $title = trim((string) ($data['group_title'] ?? ''));
        $goal = trim((string) ($data['group_goal'] ?? ''));

        if ($title === '' || $goal === '') {
            throw new \InvalidArgumentException(__('crm::marketing.validation.group_required'));
        }

        return $this->create($userId, $title, $goal);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function optionalIdFromPayload(array $data, int $userId): ?int
    {
        $groupId = (int) ($data['marketing_group_id'] ?? 0);
        $title = trim((string) ($data['group_title'] ?? ''));
        $goal = trim((string) ($data['group_goal'] ?? ''));

        if ($groupId <= 0 && $title === '' && $goal === '') {
            return null;
        }

        return $this->resolveFromPayload($data, $userId)->id;
    }
}

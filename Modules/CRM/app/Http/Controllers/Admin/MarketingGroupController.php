<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\CRM\Http\Requests\StoreMarketingGroupRequest;
use Modules\CRM\Models\MarketingGroup;
use Modules\CRM\Services\Marketing\MarketingGroupService;

class MarketingGroupController extends Controller
{
    public function __construct(
        private readonly MarketingGroupService $marketingGroupService,
    ) {
        $this->setActive('crm');
        $this->setActive('marketing');
    }

    public function create(): View
    {
        return view('crm::admin.marketing.groups.create');
    }

    public function store(StoreMarketingGroupRequest $request): RedirectResponse
    {
        $group = $this->marketingGroupService->create(
            (int) $request->user()->id,
            (string) $request->validated('title'),
            (string) $request->validated('goal'),
        );

        session()->flushMessage(true, __('crm::marketing.groups.created'));

        return redirect()->route('admin.crm.marketing.groups.show', $group);
    }

    public function show(MarketingGroup $group): View
    {
        $group->load([
            'user:id,name',
            'emailCampaigns.user:id,name',
            'whatsappCampaigns.user:id,name',
            'whatsappCampaigns.template:id,name,language',
        ])->loadCount(['emailCampaigns', 'whatsappCampaigns']);

        return view('crm::admin.marketing.groups.show', compact('group'));
    }

    public function edit(MarketingGroup $group): View
    {
        return view('crm::admin.marketing.groups.edit', compact('group'));
    }

    public function update(StoreMarketingGroupRequest $request, MarketingGroup $group): RedirectResponse
    {
        $group->update($request->validated());

        session()->flushMessage(true, __('crm::marketing.groups.updated'));

        return redirect()->route('admin.crm.marketing.groups.show', $group);
    }

    public function destroy(MarketingGroup $group): RedirectResponse
    {
        if ($group->emailCampaigns()->exists() || $group->whatsappCampaigns()->exists()) {
            session()->flushMessage(false, __('crm::marketing.groups.in_use'));

            return back();
        }

        $group->delete();

        session()->flushMessage(true, __('crm::marketing.groups.deleted'));

        return redirect()->route('admin.crm.marketing.index', ['channel' => 'campaigns']);
    }
}

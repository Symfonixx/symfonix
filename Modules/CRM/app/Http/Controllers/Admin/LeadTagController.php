<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\CRM\Http\Requests\StoreLeadTagRequest;
use Modules\CRM\Models\LeadTag;

class LeadTagController extends Controller
{
    public function __construct()
    {
        $this->setActive('crm');
        $this->setActive('crm_settings');
        $this->setActive('crm_lead_tags');
    }

    public function index(): View
    {
        $model = LeadTag::query()->ordered()->paginate(config('core.page_size'));

        return view('crm::admin.lead-tags.index', compact('model'));
    }

    public function create(): View
    {
        return view('crm::admin.lead-tags.create');
    }

    public function store(StoreLeadTagRequest $request): RedirectResponse
    {
        LeadTag::create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        session()->flushMessage(true);

        return redirect()->route('admin.crm.lead-tags.index');
    }

    public function edit(LeadTag $leadTag): View
    {
        return view('crm::admin.lead-tags.edit', ['tag' => $leadTag]);
    }

    public function update(StoreLeadTagRequest $request, LeadTag $leadTag): RedirectResponse
    {
        $leadTag->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        session()->flushMessage(true);

        return redirect()->route('admin.crm.lead-tags.index');
    }

    public function destroy(LeadTag $leadTag): RedirectResponse
    {
        $leadTag->delete();

        session()->flushMessage(true);

        return redirect()->route('admin.crm.lead-tags.index');
    }
}

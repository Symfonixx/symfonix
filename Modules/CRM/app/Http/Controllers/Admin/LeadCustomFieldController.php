<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\CRM\Http\Requests\StoreLeadCustomFieldRequest;
use Modules\CRM\Models\LeadCustomField;

class LeadCustomFieldController extends Controller
{
    public function __construct()
    {
        $this->setActive('crm');
        $this->setActive('crm_settings');
        $this->setActive('crm_custom_fields');
    }

    public function index(): View
    {
        $model = LeadCustomField::query()->ordered()->paginate(config('core.page_size'));

        return view('crm::admin.custom-fields.index', compact('model'));
    }

    public function create(): View
    {
        return view('crm::admin.custom-fields.create');
    }

    public function store(StoreLeadCustomFieldRequest $request): RedirectResponse
    {
        $data = $request->validated();

        LeadCustomField::create([
            ...$data,
            'key' => LeadCustomField::makeUniqueKey($data['label']['en'] ?? 'field'),
        ]);

        session()->flushMessage(true);

        return redirect()->route('admin.crm.custom-fields.index');
    }

    public function edit(LeadCustomField $leadCustomField): View
    {
        return view('crm::admin.custom-fields.edit', ['field' => $leadCustomField]);
    }

    public function update(StoreLeadCustomFieldRequest $request, LeadCustomField $leadCustomField): RedirectResponse
    {
        $leadCustomField->update($request->validated());

        session()->flushMessage(true);

        return redirect()->route('admin.crm.custom-fields.index');
    }

    public function destroy(LeadCustomField $leadCustomField): RedirectResponse
    {
        $leadCustomField->delete();

        session()->flushMessage(true);

        return redirect()->route('admin.crm.custom-fields.index');
    }
}

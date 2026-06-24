<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\CRM\Actions\Lead\ConvertLeadToDealAction;
use Modules\CRM\Http\Requests\ConvertLeadToDealRequest;
use Modules\CRM\Http\Requests\StoreLeadRequest;
use Modules\CRM\Http\Requests\UpdateLeadRequest;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Lead;
use Modules\CRM\Repositories\PipelineStage\PipelineStageRepository;
use Modules\Services\Models\Service;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->setActive('crm');
        $this->setActive('leads');
    }

    public function index(): View
    {
        $model = Lead::query()
            ->with(['company:id,name', 'service:id,title'])
            ->latest()
            ->paginate(config('core.page_size'));

        return view('crm::admin.lead.index', compact('model'));
    }

    public function create(): View
    {
        return view('crm::admin.lead.create', $this->formData());
    }

    public function store(StoreLeadRequest $request): RedirectResponse
    {
        Lead::create($request->validated());
        session()->flushMessage(true);

        return redirect()->route('admin.leads.index');
    }

    public function show(Lead $lead): View
    {
        $lead->loadMissing([
            'company:id,name,email,phone',
            'service:id,title',
            'assignee:id,name',
            'deal:id,title,pipeline_stage_id',
            'deal.pipelineStage:id,name,color',
            'crmActivities.user:id,name',
            'crmAuditLogs.user:id,name',
        ]);

        $stages = app(PipelineStageRepository::class)->allActive();

        return view('crm::admin.lead.show', compact('lead', 'stages'));
    }

    public function edit(Lead $lead): View
    {
        return view('crm::admin.lead.edit', array_merge(['lead' => $lead], $this->formData()));
    }

    public function update(UpdateLeadRequest $request, Lead $lead): RedirectResponse
    {
        $lead->update($request->validated());
        session()->flushMessage(true);

        return redirect()->route('admin.leads.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        Lead::destroy($request->ids);
        session()->flushMessage(true);

        return redirect()->back();
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();
        session()->flushMessage(true);

        return redirect()->route('admin.leads.index');
    }

    public function block(Lead $lead): RedirectResponse
    {
        $lead->update(['blocked' => true]);
        session()->flushMessage(true);

        return redirect()->back();
    }

    public function unblock(Lead $lead): RedirectResponse
    {
        $lead->update(['blocked' => false]);
        session()->flushMessage(true);

        return redirect()->back();
    }

    public function convertToDeal(ConvertLeadToDealRequest $request, Lead $lead, ConvertLeadToDealAction $action): RedirectResponse
    {
        $deal = $action->execute($lead, $request->validated());

        return redirect()->route('admin.deals.show', $deal->id);
    }

    private function formData(): array
    {
        return [
            'companies' => Company::query()->select(['id', 'name'])->orderBy('name')->get(),
            'services' => Service::query()->select(['id', 'title'])->orderBy('title')->get(),
            'assignees' => User::query()
                ->whereIn('type', [User::TYPE_EMPLOYEE, User::TYPE_ADMIN])
                ->select(['id', 'name', 'email'])
                ->orderBy('name')
                ->get(),
        ];
    }
}

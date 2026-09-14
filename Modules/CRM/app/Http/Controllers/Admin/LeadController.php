<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\CRM\Actions\Lead\BlockLeadAction;
use Modules\CRM\Actions\Lead\BulkDeleteLeadsAction;
use Modules\CRM\Actions\Lead\ConvertLeadToCustomerAction;
use Modules\CRM\Actions\Lead\ConvertLeadToDealAction;
use Modules\CRM\Actions\Lead\CreateLeadAction;
use Modules\CRM\Actions\Lead\DeleteLeadAction;
use Modules\CRM\Actions\Lead\ListLeadsAction;
use Modules\CRM\Actions\Lead\UnblockLeadAction;
use Modules\CRM\Actions\Lead\UpdateLeadAction;
use Modules\CRM\DTOs\Lead\LeadData;
use Modules\CRM\Http\Requests\ConvertLeadToDealRequest;
use Modules\CRM\Http\Requests\LeadIndexRequest;
use Modules\CRM\Http\Requests\StoreLeadRequest;
use Modules\CRM\Http\Requests\UpdateLeadRequest;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\LeadCustomField;
use Modules\CRM\Models\LeadTag;
use Modules\CRM\Repositories\PipelineStage\PipelineStageRepository;
use Modules\CRM\Services\Lead\LeadService;
use Modules\Services\Models\Service;
use Modules\User\Support\EmployeeAccess;

class LeadController extends Controller
{
    public function __construct(
        private readonly ListLeadsAction $listLeadsAction,
        private readonly CreateLeadAction $createLeadAction,
        private readonly UpdateLeadAction $updateLeadAction,
        private readonly DeleteLeadAction $deleteLeadAction,
        private readonly BulkDeleteLeadsAction $bulkDeleteLeadsAction,
        private readonly BlockLeadAction $blockLeadAction,
        private readonly UnblockLeadAction $unblockLeadAction,
        private readonly PipelineStageRepository $stageRepository,
        private readonly LeadService $leadService,
    ) {
        $this->setActive('crm');
        $this->setActive('leads');
    }

    public function index(LeadIndexRequest $request): View
    {
        $filters = $request->validated();
        $model = $this->listLeadsAction->execute($filters);

        return view('crm::admin.lead.index', array_merge(compact('model', 'filters'), $this->filterData()));
    }

    public function create(): View
    {
        return view('crm::admin.lead.create', $this->formData(null));
    }

    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $data = LeadData::fromRequest($request->validated());
        $lead = $this->createLeadAction->execute($data);

        if ($lead) {
            $this->leadService->syncServices($lead, $request->input('service_ids', []));
            $this->leadService->syncTags($lead, $request->input('tag_ids', []));
            $this->leadService->syncCustomFields($lead, $request->input('custom_fields', []));
            $this->leadService->storeAttachments($lead, $request->file('attachments', []));
        }

        return redirect()->route('admin.leads.index');
    }

    public function show(Lead $lead): View
    {
        $lead->loadMissing([
            'company:id,name,email,phone',
            'service:id,title',
            'services:services.id,title',
            'tags',
            'assignee:id,name',
            'deal:id,title,pipeline_stage_id',
            'deal.pipelineStage:id,name,color',
            'crmActivities' => fn ($q) => $q->with('user:id,name')->latest()->limit(50),
            'crmAuditLogs' => fn ($q) => $q->with('user:id,name')->latest('created_at')->limit(50),
        ]);

        $stages = $this->stageRepository->allActive();
        $customFields = LeadCustomField::query()->ordered()->get();

        return view('crm::admin.lead.show', compact('lead', 'stages', 'customFields'));
    }

    public function edit(Lead $lead): View
    {
        $lead->loadMissing(['services:services.id', 'tags']);

        return view('crm::admin.lead.edit', array_merge(['lead' => $lead], $this->formData($lead)));
    }

    public function update(UpdateLeadRequest $request, Lead $lead): RedirectResponse
    {
        $data = LeadData::fromRequest($request->validated());
        $updated = $this->updateLeadAction->execute($lead, $data);

        if ($updated) {
            $this->leadService->syncServices($updated, $request->input('service_ids', []));
            $this->leadService->syncTags($updated, $request->input('tag_ids', []));
            $this->leadService->syncCustomFields($updated, $request->input('custom_fields', []));
            $this->leadService->storeAttachments($updated, $request->file('attachments', []));
        }

        return redirect()->route('admin.leads.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->bulkDeleteLeadsAction->execute($request->ids);

        return redirect()->back();
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $this->deleteLeadAction->execute($lead);

        return redirect()->route('admin.leads.index');
    }

    public function block(Lead $lead): RedirectResponse
    {
        $this->blockLeadAction->execute($lead);

        return redirect()->back();
    }

    public function unblock(Lead $lead): RedirectResponse
    {
        $this->unblockLeadAction->execute($lead);

        return redirect()->back();
    }

    public function convertToDeal(ConvertLeadToDealRequest $request, Lead $lead, ConvertLeadToDealAction $action): RedirectResponse
    {
        $deal = $action->execute($lead, $request->validated());

        return redirect()->route('admin.deals.show', $deal->id);
    }

    public function convertToCustomer(Lead $lead, ConvertLeadToCustomerAction $action): RedirectResponse
    {
        $company = $action->execute($lead);

        return redirect()->route('admin.companies.show', $company);
    }

    private function formData(?Lead $lead = null): array
    {
        $assignedTagIds = $lead?->tags->pluck('id')->all() ?? [];

        return [
            'companies' => Company::query()->select(['id', 'name'])->orderBy('name')->get(),
            'services' => Service::query()->select(['id', 'title'])->orderBy('title')->get(),
            'tags' => LeadTag::query()
                ->ordered()
                ->where(function ($query) use ($assignedTagIds) {
                    $query->where('is_active', true);

                    if ($assignedTagIds !== []) {
                        $query->orWhereIn('id', $assignedTagIds);
                    }
                })
                ->get(),
            'customFields' => LeadCustomField::query()->active()->ordered()->get(),
            'assignees' => EmployeeAccess::assignableQuery()
                ->select(['id', 'name', 'email'])
                ->get(),
        ];
    }

    private function filterData(): array
    {
        return [
            'companies' => Company::query()->select(['id', 'name'])->orderBy('name')->get(),
            'tags' => LeadTag::query()->active()->ordered()->get(),
            'assignees' => EmployeeAccess::assignableQuery()
                ->select(['id', 'name'])
                ->get(),
        ];
    }
}

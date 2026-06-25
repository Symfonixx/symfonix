<?php

namespace Modules\CRM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Http\Requests\DeleteMultiRequest;
use Modules\CRM\Actions\Deal\BulkDeleteDealsAction;
use Modules\CRM\Actions\Deal\CreateDealAction;
use Modules\CRM\Actions\Deal\DeleteDealAction;
use Modules\CRM\Actions\Deal\ListDealsAction;
use Modules\CRM\Actions\Deal\ListDealsKanbanAction;
use Modules\CRM\Actions\Deal\MoveDealStageAction;
use Modules\CRM\Actions\Deal\UpdateDealAction;
use Modules\CRM\DTOs\Deal\DealData;
use Modules\CRM\Http\Requests\DealIndexRequest;
use Modules\CRM\Http\Requests\MoveDealStageRequest;
use Modules\CRM\Http\Requests\StoreDealRequest;
use Modules\CRM\Http\Requests\UpdateDealRequest;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Deal;
use Modules\CRM\Repositories\PipelineStage\PipelineStageRepository;
use Modules\User\Support\EmployeeAccess;

class DealController extends Controller
{
    public function __construct(
        private readonly ListDealsAction $listDealsAction,
        private readonly ListDealsKanbanAction $listDealsKanbanAction,
        private readonly CreateDealAction $createDealAction,
        private readonly UpdateDealAction $updateDealAction,
        private readonly DeleteDealAction $deleteDealAction,
        private readonly BulkDeleteDealsAction $bulkDeleteDealsAction,
        private readonly MoveDealStageAction $moveDealStageAction,
        private readonly PipelineStageRepository $stageRepository,
    ) {
        $this->authorizeResource(Deal::class, 'deal');
        $this->setActive('crm');
        $this->setActive('deals');
    }

    public function index(DealIndexRequest $request)
    {
        $filters = $request->validated();
        $view = $filters['view'] ?? 'list';

        if ($view === 'kanban') {
            $stages = $this->listDealsKanbanAction->execute($filters);

            return view('crm::admin.deal.kanban', compact('stages', 'filters'));
        }

        $model = $this->listDealsAction->execute($filters);
        $stages = $this->stageRepository->allActive();

        return view('crm::admin.deal.index', compact('model', 'stages', 'filters'));
    }

    public function create()
    {
        return view('crm::admin.deal.create', $this->formData());
    }

    public function store(StoreDealRequest $request): RedirectResponse
    {
        $data = DealData::fromRequest($request->validated());
        $this->createDealAction->execute($data);

        return redirect()->route('admin.deals.index');
    }

    public function show(Deal $deal)
    {
        $deal->loadMissing([
            'company:id,name',
            'pipelineStage:id,name,color',
            'assignee:id,name,email',
            'lead:id,name,email',
            'stageHistories.fromStage:id,name,color',
            'stageHistories.toStage:id,name,color',
            'stageHistories.changedBy:id,name',
            'crmActivities.user:id,name',
            'crmAuditLogs.user:id,name',
        ]);

        $stages = $this->stageRepository->allActive();

        return view('crm::admin.deal.show', compact('deal', 'stages'));
    }

    public function edit(Deal $deal)
    {
        return view('crm::admin.deal.edit', array_merge(['deal' => $deal], $this->formData()));
    }

    public function update(UpdateDealRequest $request, Deal $deal): RedirectResponse
    {
        $data = DealData::fromRequest($request->validated());
        $this->updateDealAction->execute($deal, $data);

        return redirect()->route('admin.deals.index');
    }

    public function destroy(Deal $deal): RedirectResponse
    {
        $this->deleteDealAction->execute($deal);

        return redirect()->route('admin.deals.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->bulkDeleteDealsAction->execute($request->input('ids', []));

        return back();
    }

    public function moveStage(MoveDealStageRequest $request, Deal $deal): RedirectResponse
    {
        $this->authorize('update', $deal);

        $validated = $request->validated();
        $this->moveDealStageAction->execute(
            $deal,
            (int) $validated['pipeline_stage_id'],
            $validated['notes'] ?? null
        );

        return back();
    }

    private function formData(): array
    {
        return [
            'companies' => Company::query()->select(['id', 'name'])->orderBy('name')->get(),
            'stages' => $this->stageRepository->allActive(),
            'assignees' => $this->assignees(),
            'defaultStageId' => $this->stageRepository->findDefault()?->id,
        ];
    }

    private function assignees(): Collection
    {
        return EmployeeAccess::assignableQuery()
            ->select(['id', 'name', 'email'])
            ->get();
    }
}
